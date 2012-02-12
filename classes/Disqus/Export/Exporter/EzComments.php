<?php
/**
 * File containing the EzComments class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export\Exporter;
use Disqus\Export\ExporterInterface,
    Disqus\Export\Thread,
    Disqus\Export\Comment,
    \eZPersistentObject,
    \ezcomComment,
    \ezcomCommentsType,
    \eZContentObject,
    \eZURI,
    \eZDB,
    \eZINI,
    \eZSSLZone,
    \eZSys,
    \DateTime,
    \DateTimeZone;

/**
 * Exporter for eZ Comments
 */
class EzComments implements ExporterInterface
{
    /**
     * Array of commented contentobject ids
     *
     * @var array
     */
    private $contentObjectIds = array();

    private $rowIndex = 0;

    private $rowCount;

    public function __construct()
    {

    }

    /**
     * Returns the exporter human readable name.
     *
     * @return string
     */
    public function exporterName()
    {
        return 'eZ Comments exporter';
    }

    /**
     * Initializes the exporter.
     * Do here everything you need to do before the export process to begin
     * (e.g. connecting to DB, loading configuration files...)
     *
     * @return void
     */
    public function initialize()
    {
        $limit = null;
        $db = eZDB::instance();
        foreach ( $db->arrayQuery( 'SELECT DISTINCT contentobject_id FROM ezcomment' ) as $row )
        {
            $this->contentObjectIds[] = $row['contentobject_id'];
        }

        $this->rowCount = count( $this->contentObjectIds );
    }

    /**
     * Returns the total number of comments to export.
     *
     * @return int
     */
    public function getCommentsCount()
    {
        return eZPersistentObject::count( ezcomComment::definition() );
    }

    /**
     * Returns the total number of threads
     *
     * @return int
     */
    public function getThreadsCount()
    {
        return $this->rowCount;
    }

    /**
     * Returns the next thread to export comments from.
     * This thread object might reflect an eZ Publish content object.
     *
     * If there is no more thread to process, this method will return false.
     *
     * @return \Disqus\Export\Thread|false
     */
    public function getNextThread()
    {
        if ( $this->rowIndex < $this->rowCount )
        {
            $contentObject = eZContentObject::fetch( $this->contentObjectIds[$this->rowIndex] );
            $dm = $contentObject->dataMap();
            $ezcommentsAttribute = null;
            foreach ( $dm as $attribute )
            {
                if ( $attribute->attribute( 'data_type_string' ) === ezcomCommentsType::DATA_TYPE_STRING )
                {
                    $ezcommentsAttribute = $atttribute;
                    break;
                }
            }

            // Building the Thread object
            // title and content properties get the same content since $thread->content is not really important
            $thread = new Thread;
            $thread->title = $thread->content = $contentObject->name();
            $thread->identifier = $contentObject->attribute( 'id' );
            $thread->link = $this->generateThreadLinkByContentObject( $contentObject );
            $thread->postDate = new DateTime(
                $contentObject->attribute( 'published' ),
                new DateTimeZone( 'gmt' )
            );
            // Check if comments are open or closed
            $thread->commentsEnabled = $ezcommentsAttribute->attribute( 'data_int' ) == 1;

            eZContentObject::clearCache( array( $contentObject->attribute( 'id' ) ) );
            $this->rowIndex++;

            return $thread;
        }

        return false;
    }

    /**
     * Generates absolute link for thread (content object), taking care of SSL zones when applyable
     *
     * @param eZContentObject $contentObject Content object to generate link for
     * @return string
     */
    protected function generateThreadLinkByContentObject( eZContentObject $contentObject )
    {
        $ini = eZINI::instance();
        $protocol = 'http://';
        $portString = '';
        $host = $ini->variable( 'SiteSettings', 'SiteURL' );
        if ( eZSSLZone::checkNode( 'content', 'view', $contentObject->mainNode(), false ) === true )
        {
            $protocol = 'https://';
            $sslPort = $ini->variable( 'SiteSettings', 'SSLPort' );
            $portString = ( $sslPort == eZSSLZone::DEFAULT_SSL_PORT ) ? '' : ":$sslPort";
        }

        return $protocol . $host . eZSys::indexDir( false ) . '/' . $contentObject->mainNode()->urlAlias();
    }

    /**
     * Returns all comments for provided $thread object as an array of {@link \Disqus\Export\Comment objects}
     *
     * @return \Disqus\Export\Comment[]
     */
    public function getCommentsByThread( Thread $thread )
    {
        $comments = array();
        // Using fetchByContentObjectIDList here because fetchByContentObjectID() has language as mandatory param.
        $ezcomments = ezcomComment::fetchByContentObjectIDList( array( $thread->identifier ) );
        foreach ( $ezcomments as $ezcomment )
        {
            $comments[] = $this->buildCommentFromEzComment( $ezcomment );
        }

        unset( $ezcomments );
        return $comments;
    }

    /**
     * @param \ezcomComment $ezcomment
     * @return \Disqus\Export\Comment
     */
    protected function buildCommentFromEzComment( ezcomComment $ezcomment )
    {
        $comment = new Comment;
        $comment->id = $ezcomment->attribute( 'id' );
        $comment->authorName = $ezcomment->attribute( 'name' );
        $comment->authorMail = $ezcomment->attribute( 'email' );
        $comment->authorIp = $ezcomment->attribute( 'ip' );
        $comment->authorUrl = $ezcomment->attribute( 'url' );
        $comment->date = new DateTime(
            $ezcomment->attribute( 'modified' ),
            new DateTimeZone( 'gmt' )
        );
        $comment->content = $ezcomment->attribute( 'text' );
        $comment->isApproved = $ezcomment->attribute( 'status' ) == 1;

        return $comment;
    }

    /**
     * Final method called at the end of the export process.
     *
     * @return void
     */
    public function cleanup()
    {

    }
}
