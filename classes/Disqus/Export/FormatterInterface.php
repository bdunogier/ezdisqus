<?php
/**
 * File containing the FormatterInterface class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Interface for Disqus formatters
 */
interface FormatterInterface
{
    /**
     * Initializes the formatter.
     * If using XML, you might create DOMDocument, root nodes...
     *
     * @return void
     */
    public function initialize();

    /**
     * Formats a $thread with its $comments
     *
     * @param \Disqus\Export\Thread $thread
     * @param \Disqus\Export\Comment[] $comments
     * @return void
     */
    public function formatThread( Thread $thread, array $comments );

    /**
     * Renders formatted exported threads and comments as string.
     *
     * @return string
     */
    public function renderString();

    /**
     * Returns the internal formatted data.
     * e.g. DOMDocument object if working with XML
     *
     * @return mixed
     */
    public function getData();

    /**
     * Returns splitter object used to split exported data into several parts
     *
     * @return \Disqus\Export\SplitterInterface
     */
    public function getSplitter();

    /**
     * Returns export format.
     * Will be use as export file suffix.
     *
     * @return string
     */
    public function getFormat();

    /**
     * Returns the formatter human readable name.
     *
     * @return string
     */
    public function getName();
}
