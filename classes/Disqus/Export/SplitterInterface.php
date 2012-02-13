<?php
/**
 * File containing the SplitterInterface class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Interface for Disqus formatters
 */
interface SplitterInterface
{
    /**
     * Splits the exported data into several portions and stores it in $splittedDataStruct
     *
     * @param \Disqus\Export\SplittedData
     * @return void
     */
    public function split( SplittedData $splittedDataStruct );
}
