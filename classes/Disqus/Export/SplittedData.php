<?php
/**
 * File containing the SplittedData class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Struct holding splitted exported data
 */
class SplittedData
{
    /**
     * Array of splitted exported data, as string.
     * e.g. Several XML strings
     *
     * @var string[]
     */
    public $stringArray;

    /**
     * Total size of all exported data, in bytes
     *
     * @var int
     */
    public $totalSize = 0;
}
