<?php
/**
 * File containing the Process class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Generic processor class for exporting comments in Disqus format
 */
class Processor
{
    /**
     * @var \Disqus\Export\ExporterInterface
     */
    protected $exporter;

    /**
     * @var \Disqus\Export\FormatterInterface
     */
    protected $formatter;

    /**
     * Builds a new export processor.
     *
     * @param \Disqus\Export\ExporterInterface $exporter Exporter to use by the processor
     * @param \Disqus\Export\FormatterInterface $formatter Formatter to use by the processor
     */
    public function __construct( ExporterInterface $exporter, FormatterInterface $formatter )
    {
        $this->exporter = $exporter;
        $this->formatter = $formatter;
    }

    /**
     * Starts the export process.
     *
     * @return void
     */
    public function export()
    {
        $this->exporter->initialize();
        $this->formatter->initialize();

        while ( $thread = $this->exporter->getNextThread() )
        {
            $comments = $this->exporter->getCommentsByThread( $thread );
            $this->formatter->formatThread( $thread, $comments );
        }

        $this->exporter->cleanup();
    }

    /**
     * Returns exported data, formatted.
     * Returned value depends on your formatter.
     *
     * @return mixed
     */
    public function getExportedData()
    {
        return $this->formatter->getData();
    }

    /**
     * Returns the string representation of exported data
     *
     * @return string
     */
    public function render()
    {
        return $this->formatter->renderString();
    }

    public function split()
    {
        $splittedData = new SplittedData;
        $splitter = $this->formatter->getSplitter();
        $splitter->split( $splittedData );
        return $splittedData;
    }
}
