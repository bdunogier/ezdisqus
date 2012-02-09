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

    }
}
