<?php
/**
 * File containing the NativeComments class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export\Exporter;
use Disqus\Export\ExporterInterface,
    Disqus\Export\Thread,
    Disqus\Export\Comment,
    \eZContentObjectTreeNode,
    \eZINI;

/**
 * Exporter for eZ Comments
 */
class NativeComments implements ExporterInterface
{
    /**
     * Array of potentially commented nodes
     *
     * @var array[(array)ezcontentobjecttreenode]
     */
    private $nodes = array();

    private $rowIndex = 0;

    private $rowCount;

    /**
     * Comments data provider
     * @var \Disqus\Export\NativeComments\DataProvider
     */
    private $dataProvider;

    /**
     * Export classes
     * @var array( identifier => boolean_attribute )
     */
    private $exportClasses = array();

    public function __construct()
    {
    }

    /**
     * Returns the exporter human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Native eZ Publish comments';
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
        // select all node ID that match the export settings
        foreach( eZINI::instance( 'disqus.ini' )->variable( 'NativeCommentsExporterSettings', 'Classes' ) as $exportClassIdentifier )
        {
            if ( strstr( $exportClassIdentifier, '/' ) !== false )
                list( $exportClassIdentifier, $exportBooleanAttribute ) = explode( '/', $exportClassIdentifier );
            else
                $exportBooleanAttribute = null;

            $exportClasses[$exportClassIdentifier] = $exportBooleanAttribute;

            $callParams = array(
                'ClassIdentifier' => $exportClass,
                'AsObject' => false,
            );

            if ( isset( $exportBoolean ) )
            {
                $callParams['AttributeFilter'][] = array(
                    array( $exportBoolean, '=', 1 ),
                );
            }
            $commentedNodes = eZContentObjectTreeNode::subTreeByNodeID( $callParams, 1 );
        }

        $this->nodes = $commentedNodes;
    }

    /**
     * Returns the total number of comments to export.
     *
     * @return int
     */
    public function getCommentsCount()
    {
        return eZContentObjectTreeNode::subTreeCountByNodeID(
            array( 'ClassIdentifier' => 'comment'),
            1
        );
    }

    /**
     * Returns the total number of threads
     *
     * @return int
     */
    public function getThreadsCount()
    {
        // Yeah, like this is getting implemented ;)
        // Maybe later
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
        while( $nodeId = next( $this->nodes ) )
        {
            // fetch the next node from the prefiltered list
            $node = eZContentObjectTreeNode::fetch( $nodeId );
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;

            // fetch the comments under it, and bail out if there are none
            $comments = eZContentObjectTreeNode::subTreeCountByNodeID(
                array( 'ClassIdentifier' => 'comment' ),
                $nodeId
            );

            if ( !count( $comments ) )
                continue;

            // Check for boolean attribute
            $object = $node->object();
            $classIdentifier = $object->attribute('identifier');
            if ( isset( $this->exportClasses[$classIdentifier] ) && $this->exportClasses[$classIdentifier] !== null )
            {
                $dataMap = $object->dataMap();
                if ( !isset( $dataMap[$this->exportClasses[$classIdentifier] ] ) )
                    throw new InvalidArgumentException( "unknown attribute {$this->exportClasses[$classIdentifier]}" );

                // boolean is false
                if ( !$dataMap[$this->exportClasses[$classIdentifier]]->attribute( 'content' ) )
                    continue;
            }

            // Building the Thread object
            // title and content properties get the same content since $thread->content is not really important
            $thread = new Thread;
            $thread->title = $thread->content = $object->name();
            $thread->identifier = $object->attribute( 'id' );

            // @todo change, we have a node here
            $thread->link = $this->generateThreadLinkByContentObject( $object );
            $thread->postDate = new DateTime(
                '@' . $node->object()->attribute( 'published' ),
                new DateTimeZone( 'gmt' )
            );

            eZContentObject::clearCache( array( $contentObject->attribute( 'id' ) ) );

            return $thread;
        }

        return false;
    }

    /**
     * Generates absolute link for thread $node, taking care of SSL zones when applyable
     *
     * @param eZContentObjectTreeNode $node Node to generate link for
     * @return string
     */
    protected function generateThreadLinkByContentObjectTreeNode( eZContentObjectTreeNode $node )
    {
        $ini = eZINI::instance();
        $protocol = 'http://';
        $portString = '';
        $host = $ini->variable( 'SiteSettings', 'SiteURL' );
        if ( eZSSLZone::checkNode( 'content', 'view', $node, false ) === true )
        {
            $protocol = 'https://';
            $sslPort = $ini->variable( 'SiteSettings', 'SSLPort' );
            $portString = ( $sslPort == eZSSLZone::DEFAULT_SSL_PORT ) ? '' : ":$sslPort";
        }

        return $protocol . $host . $portString . eZSys::indexDir( false ) . '/' . $node->urlAlias();
    }

    /**
     * Returns all comments for provided $thread object as an array of {@link \Disqus\Export\Comment objects}
     *
     * @return \Disqus\Export\Comment[]
     */
    public function getCommentsByThread( Thread $thread )
    {
        $comments = array();

        $commentNodes = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassIdentifier' => array_keys( $this->exportClasses ) ),
            $thread->identifier
        );
        foreach ( $commentNodes as $commantNode )
        {
            $comments[] = $this->buildCommentFromNode( $coimmentNode );
        }

        return $comments;
    }

    /**
     * @param \ezcomComment $ezcomment
     * @return \Disqus\Export\Comment
     */
    protected function buildCommentFromNode( eZContentObjectTreeNode $node )
    {
        $comment = new Comment;
        $comment->id = $node->attribute( 'node_id' );
        // $comment->authorName = $ezcomment->attribute( 'name' );
        // $comment->authorMail = $ezcomment->attribute( 'email' );
        // $comment->authorIp = $ezcomment->attribute( 'ip' );
        // $comment->authorUrl = $ezcomment->attribute( 'url' );
        $comment->date = new DateTime(
            '@' . $node->attribute( 'published' ),
            new DateTimeZone( 'gmt' )
        );
        // $comment->content = $ezcomment->attribute( 'text' );
        // $comment->isApproved = $ezcomment->attribute( 'status' ) == 1;

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
