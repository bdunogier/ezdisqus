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
     * Exporter to use by the processor
     *
     * @var \Disqus\Export\ExporterInterface
     */
    protected $exporter;

    public function __construct( ExporterInterface $exporter )
    {
        $this->exporter = $exporter;
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
