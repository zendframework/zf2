<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\ScrollingStyle;

use Zend\Paginator;
use Zend\Paginator\ScrollingStyle;

/**
 * A scrolling style in which the cursor advances to the upper bound
 * of the page range, the page range "jumps" to the next section, and
 * the cursor moves back to the beginning of the range.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Jumping implements ScrollingStyle
{
    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @param  \Zend\Paginator\Paginator $paginator
     * @param  integer $pageRange Unused
     * @return array
     */
    public function getPages(Paginator\Paginator $paginator, $pageRange = null)
    {
        $pageRange  = $paginator->getPageRange();
        $pageNumber = $paginator->getCurrentPageNumber();

        $delta = $pageNumber % $pageRange;

        if ($delta == 0) {
            $delta = $pageRange;
        }

        $offset     = $pageNumber - $delta;
        $lowerBound = $offset + 1;
        $upperBound = $offset + $pageRange;

        return $paginator->getPagesInRange($lowerBound, $upperBound);
    }
}
