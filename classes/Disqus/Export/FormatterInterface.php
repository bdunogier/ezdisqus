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
class FormatterInterface
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
}
