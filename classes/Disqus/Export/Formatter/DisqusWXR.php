<?php
/**
 * File containing the DisqusWXR class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export\Formatter;
use Disqus\Export\FormatterInterface,
    Disqus\Export\Thread,
    Disqus\Export\Comment,
    Disqus\Export\SplitterInterface,
    Disqus\Export\SplittedData,
    \eZINI,
    \DOMDocument;

/**
 * Formatter for Disqus WXR (Wordpress eXtended RSS) format.
 * @see http://docs.disqus.com/developers/export/import_format/
 */
class DisqusWXR implements FormatterInterface, SplitterInterface
{
    private $debug;

    private $rootNS = array(
        'content' => 'http://purl.org/rss/1.0/modules/content/',
        'dsq'     => 'http://www.disqus.com',
        'dc'      => 'http://purl.org/dc/elements/1.1/',
        'wp'      => 'http://wordpress.org/export/1.0/'
    );

    /**
     * DOM document
     *
     * @var \DOMDocument
     */
    private $xmlDoc;

    /**
     * <channel> tag DOMElement object
     *
     * @var \DOMElement
     */
    private $channelTag;

    public function __construct( $debug = false )
    {
        $this->debug = $debug;
    }

    /**
     * Initializes the formatter.
     * If using XML, you might create DOMDocument, root nodes...
     *
     * @return void
     */
    public function initialize()
    {
        $this->xmlDoc = $this->generateDomDocument();
        $this->channelTag = $this->xmlDoc->createElement( 'channel' );
        $this->xmlDoc->firstChild->appendChild( $this->channelTag );
    }

    /**
     * Generates valid DOMDocument and appends its root <rss> tag
     *
     * @return \DOMDocument
     */
    private function generateDomDocument()
    {
        $xmlDoc = new DOMDocument( '1.0', 'UTF-8' );
        $xmlDoc->formatOutput = $this->debug;
        $root = $xmlDoc->createElement( 'rss' );
        $root->setAttribute( 'version', '2.0' );
        foreach ( $this->rootNS as $ns => $nsUrl )
        {
            $root->setAttributeNS(
                'http://www.w3.org/2000/xmlns/', // xmlns namespace URL
                "xmlns:$ns",
                $nsUrl
            );
        }
        $xmlDoc->appendChild( $root );
        return $xmlDoc;
    }

    /**
     * Formats a $thread with its $comments
     *
     * @param \Disqus\Export\Thread $thread
     * @param \Disqus\Export\Comment[] $comments
     * @return void
     */
    public function formatThread( Thread $thread, array $comments )
    {
        $item = $this->xmlDoc->createElement( 'item' );
        $this->channelTag->appendChild( $item );

        $item->appendChild( $this->xmlDoc->createElement( 'title', htmlspecialchars( $thread->title ) ) );
        $item->appendChild( $this->xmlDoc->createElement( 'link', $thread->link ) );
        $contentThread = $this->xmlDoc->createElementNS( $this->rootNS['content'], 'content:encoded' );
        $contentThread->appendChild( $this->xmlDoc->createCDATASection( $thread->content ) );
        $item->appendChild( $contentThread );
        $item->appendChild(
            $this->xmlDoc->createElementNS(
                $this->rootNS['dsq'],
                'dsq:thread_identifier',
                $thread->identifier
            )
        );
        $item->appendChild(
            $this->xmlDoc->createElementNS(
                $this->rootNS['wp'],
                'wp:post_date_gmt',
                $thread->postDate->format( 'Y-m-d H:i:s' )
            )
        );
        $item->appendChild(
            $this->xmlDoc->createElementNS(
                $this->rootNS['wp'],
                'wp:comment_status',
                $thread->commentsEnabled ? 'open' : 'closed'
            )
        );

        // Now render the comments
        foreach ( $comments as $comment )
        {
            $item->appendChild( $this->formatComment( $comment ) );
        }
    }

    /**
     * Formats $comment object into the correct XML node
     *
     * @todo Handle SSO <dsq:remote> tag (see {@link https://github.com/lolautruche/eZDisqus/issues/19})
     * @param \Disqus\Export\Comment $comment
     * @return DOMElement
     */
    protected function formatComment( Comment $comment )
    {
        $commentNode = $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment' );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_id', $comment->id )
        );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_author', htmlspecialchars( $comment->authorName ) )
        );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_author_email', $comment->authorMail )
        );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_author_url', $comment->authorUrl )
        );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_author_IP', $comment->authorIp )
        );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_date_gmt', $comment->date->format( 'Y-m-d H:i:s' ) )
        );
        $content = $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_content' );
        $content->appendChild(
            $this->xmlDoc->createCDATASection( $comment->content )
        );
        $commentNode->appendChild( $content );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS(
                $this->rootNS['wp'],
                'wp:comment_approved',
                $comment->isApproved ? '1' : '0'
            )
        );
        $commentNode->appendChild(
            $this->xmlDoc->createElementNS( $this->rootNS['wp'], 'wp:comment_parent', $comment->parentId )
        );

        return $commentNode;
    }

    /**
     * Renders formatted exported threads and comments as string.
     *
     * @return string
     */
    public function renderString()
    {
        return $this->xmlDoc->saveXML();
    }

    /**
     * Returns the internal formatted data.
     * e.g. DOMDocument object if working with XML
     *
     * @return DOMDocument
     */
    public function getData()
    {
        return $this->xmlDoc;
    }

    /**
     * Returns splitter object used to split exported data into several parts
     *
     * @return \Disqus\Export\SplitterInterface
     */
    public function getSplitter()
    {
        return $this;
    }

    /**
     * Splits the exported data into several portions and stores it in $splittedDataStruct
     *
     * @param \Disqus\Export\SplittedData
     * @return void
     */
    public function split( SplittedData $splittedDataStruct )
    {
        $maxSize = (int)eZINI::instance( 'disqus.ini' )->variable( 'ExportSettings', 'MaxFileSize' );
        $xmlString = $this->renderString();
        if ( function_exists( 'mb_strlen' ) )
            $fileSize = mb_strlen( $xmlString );
        else
            $fileSize = strlen( $xmlString );

        var_dump( $maxSize, $fileSize );
        $numberOfFiles = ceil( $fileSize / $maxSize );
        $splittedDataStruct->totalSize = $fileSize;
        $itemNodeList = $this->xmlDoc->getElementsByTagName( 'item' );
        $nodeIndex = 0;
        for ( $i = 0; $i < $numberOfFiles; ++$i )
        {
            $doc = $this->generateDomDocument();
            $channel = $doc->createElement( 'channel' );
            $doc->firstChild->appendChild( $channel );
            $maxIndex = $nodeIndex + ceil( $itemNodeList->length / $numberOfFiles );
            // The double condition in while ensures that the loop will end if we reach the end of the $itemNodeList.
            // Otherwise $itemNodeList->item() would not return a valid DOMNode
            while ( $nodeIndex < $itemNodeList->length && $nodeIndex < $maxIndex )
            {
                $newNode = $doc->importNode( $itemNodeList->item( $nodeIndex ), true );
                $channel->appendChild( $newNode );
                $nodeIndex++;
            }

            $splittedDataStruct->stringArray[] = $doc->saveXML();
        }
    }
}
