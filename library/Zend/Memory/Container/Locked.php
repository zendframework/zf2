<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Memory
 */

namespace Zend\Memory\Container;

/**
 * Memory value container
 *
 * Locked (always stored in memory).
 *
 * @category   Zend
 * @package    Zend_Memory
 */
class Locked extends AbstractContainer
{
    /**
     * Value object
     *
     * @var string
     */
    public $value;


    /**
     * Object constructor
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Lock object in memory.
     */
    public function lock()
    {
        /* Do nothing */
    }

    /**
     * Unlock object
     */
    public function unlock()
    {
        /* Do nothing */
    }

    /**
     * Return true if object is locked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return true;
    }

    /**
     * Get string value reference
     *
     * _Must_ be used for value access before PHP v 5.2
     * or _may_ be used for performance considerations
     *
     * @return &string
     */
    public function &getRef()
    {
        return $this->value;
    }

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch()
    {
        /* Do nothing */
    }

    /**
     * Destroy memory container and remove it from memory manager list
     */
    public function destroy()
    {
        /* Do nothing */
    }
}
