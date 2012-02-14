<?php
/**
 * File containing the ExporterInterface class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Interface for Disqus exporters.
 * Any exporter must implement it to work properly with {\Disqus\Export\Process}
 */
interface ExporterInterface
{
    /**
     * Returns the exporter human readable name.
     *
     * @return string
     */
    public function getName();

    /**
     * Initializes the exporter.
     * Do here everything you need to do before the export process to begin
     * (e.g. connecting to DB, loading configuration files...)
     *
     * @return void
     */
    public function initialize();

    /**
     * Returns the total number of comments to export.
     *
     * @return int
     */
    public function getCommentsCount();

    /**
     * Returns the total number of threads
     *
     * @return int
     */
    public function getThreadsCount();

    /**
     * Returns the next thread to export comments from.
     * This thread object might reflect an eZ Publish content object.
     *
     * If there is no more thread to process, this method will return false.
     *
     * @return \Disqus\Export\Thread|false
     */
    public function getNextThread();

    /**
     * Returns all comments for provided $thread object as an array of {@link \Disqus\Export\Comment objects}
     *
     * @return \Disqus\Export\Comment[]
     */
    public function getCommentsByThread( Thread $thread );

    /**
     * Final method called at the end of the export process.
     *
     * @return void
     */
    public function cleanup();
}
