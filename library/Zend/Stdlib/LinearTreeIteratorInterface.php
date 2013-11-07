<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

/**
 * Interface to allow linear tree iteration
 */
interface LinearTreeIteratorInterface extends \Iterator
{
    /**
     * Get value from depth field of current row
     *
     * @return string
     */
    public function getDepth();

    /**
     * Set depth field name
     *
     * @param string $depthField
     * @return self
     */
    public function setDepthField($depthField);

}
