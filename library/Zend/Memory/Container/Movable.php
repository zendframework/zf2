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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Memory\Container;
use Zend\Memory,
    Zend\Memory\Exception;

/**
 * Memory value container
 *
 * Movable (may be swapped with specified backend and unloaded).
 *
 * @uses       \Zend\Memory\Container\AbstractContainer
 * @uses       \Zend\Memory\Exception
 * @uses       \Zend\Memory\Value
 * @category   Zend
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Movable extends AbstractContainer
{
    /**
     * Internal object Id
     *
     * @var integer
     */
    protected $_id;

    /**
     * Memory manager reference
     *
     * @var \Zend\Memory\MemoryManager
     */
    private $_memManager;

    /**
     * Value object
     *
     * @var \Zend\Memory\Value
     */
    private $_value;

    /** Value states */
    const LOADED   = 1;
    const SWAPPED  = 2;
    const LOCKED   = 4;

    /**
     * Value state (LOADED/SWAPPED/LOCKED)
     *
     * @var integer
     */
    private $_state;

    /**
     * Object constructor
     *
     * @param \Zend\Memory\MemoryManager $memoryManager
     * @param integer $id
     * @param string $value
     */
    public function __construct(Memory\MemoryManager $memoryManager, $id, $value)
    {
        $this->_memManager = $memoryManager;
        $this->_id    = $id;
        $this->_state = self::LOADED;
        $this->_value = new Memory\Value($value, $this);
    }

    /**
     * Lock object in memory.
     */
    public function lock()
    {
        if ( !($this->_state & self::LOADED) ) {
            $this->_memManager->load($this, $this->_id);
            $this->_state |= self::LOADED;
        }

        $this->_state |= self::LOCKED;

        /**
         * @todo
         * It's possible to set "value" container attribute to avoid modification tracing, while it's locked
         * Check, if it's  more effective
         */
    }

    /**
     * Unlock object
     */
    public function unlock()
    {
        // Clear LOCKED state bit
        $this->_state &= ~self::LOCKED;
    }

    /**
     * Return true if object is locked
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->_state & self::LOCKED;
    }

    /**
     * Get handler
     *
     * Loads object if necessary and moves it to the top of loaded objects list.
     * Swaps objects from the bottom of loaded objects list, if necessary.
     *
     * @param string $property
     * @return string
     * @throws \Zend\Memory\Exception
     */
    public function __get($property)
    {
        if ($property != 'value') {
            throw new Exception\InvalidArgumentException('Unknown property: \Zend\Memory\Container\Movable::$' . $property);
        }

        if ( !($this->_state & self::LOADED) ) {
            $this->_memManager->load($this, $this->_id);
            $this->_state |= self::LOADED;
        }

        return $this->_value;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  string $value
     * @throws Exception\InvalidArgumentException
     */
    public function __set($property, $value)
    {
        if ($property != 'value') {
            throw new Exception\InvalidArgumentException('Unknown property: \Zend\Memory\Container\Movable::$' . $property);
        }

        $this->_state = self::LOADED;
        $this->_value = new Memory\Value($value, $this);

        $this->_memManager->processUpdate($this, $this->_id);
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
        if ( !($this->_state & self::LOADED) ) {
            $this->_memManager->load($this, $this->_id);
            $this->_state |= self::LOADED;
        }

        return $this->_value->getRef();
    }

    /**
     * Signal, that value is updated by external code.
     *
     * Should be used together with getRef()
     */
    public function touch()
    {
        $this->_memManager->processUpdate($this, $this->_id);
    }

    /**
     * Process container value update.
     * Must be called only by value object
     *
     * @internal
     */
    public function processUpdate()
    {
        // Clear SWAPPED state bit
        $this->_state &= ~self::SWAPPED;

        $this->_memManager->processUpdate($this, $this->_id);
    }

    /**
     * Start modifications trace
     *
     * @internal
     */
    public function startTrace()
    {
        if ( !($this->_state & self::LOADED) ) {
            $this->_memManager->load($this, $this->_id);
            $this->_state |= self::LOADED;
        }

        $this->_value->startTrace();
    }

    /**
     * Set value (used by memory manager when value is loaded)
     *
     * @internal
     */
    public function setValue($value)
    {
        $this->_value = new Memory\Value($value, $this);
    }

    /**
     * Clear value (used by memory manager when value is swapped)
     *
     * @internal
     */
    public function unloadValue()
    {
        // Clear LOADED state bit
        $this->_state &= ~self::LOADED;

        $this->_value = null;
    }

    /**
     * Mark, that object is swapped
     *
     * @internal
     */
    public function markAsSwapped()
    {
        // Clear LOADED state bit
        $this->_state |= self::LOADED;
    }

    /**
     * Check if object is marked as swapped
     *
     * @internal
     * @return boolean
     */
    public function isSwapped()
    {
        return $this->_state & self::SWAPPED;
    }

    /**
     * Get object id
     *
     * @internal
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Destroy memory container and remove it from memory manager list
     *
     * @internal
     */
    public function destroy()
    {
        /**
         * We don't clean up swap because of performance considerations
         * Cleaning is performed by Memory Manager destructor
         */

        $this->_memManager->unlink($this, $this->_id);
    }
}
