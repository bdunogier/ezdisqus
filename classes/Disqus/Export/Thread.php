<?php
/**
 * File containing the Thread class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Thread value object
 */
class Thread
{
    /**
     * Title of thread.
     * Basically content object name
     *
     * @var string
     */
    public $title;

    /**
     * Absolute URL to thread
     * e.g. http://foo.com/example
     *
     * @var string
     */
    public $link;

    /**
     * Thread content.
     * Basically the thread abstract. HTML is allowed.
     *
     * @var string
     */
    public $content;

    /**
     * Thread identifier.
     * If thread is a content object, must be ContentObjectID
     *
     * @var mixed
     */
    public $identifier;

    /**
     * Published date for thread.
     * Must be GMT.
     *
     * @var \DateTime
     */
    public $postDate;

    /**
     * Flag to tell if comments are enabled for thread.
     * Default is false.
     *
     * @var bool
     */
    public $commentsEnabled = false;
}
