<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Memory;

use Zend\Cache\StorageFactory as CacheFactory;
use Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter;
use Zend\Memory;
use Zend\Memory\Container;

/**
 * @group      Zend_Memory
 */
class AccessControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Cache object
     *
     * @var CacheAdapter
     */
    private $_cache = null;

    public function setUp()
    {
        $this->_cache = CacheFactory::adapterFactory('memory', ['memory_limit' => 0]);
    }

    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager  = new Memory\MemoryManager($this->_cache);
        $memObject      = $memoryManager->create('012345678');

        $this->assertInstanceOf('Zend\Memory\Container\AccessController', $memObject);
    }

    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager  = new Memory\MemoryManager($this->_cache);
        $memObject      = $memoryManager->create('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string) $memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string) $memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertInstanceOf('Zend\Memory\Value', $memObject->value);
        $this->assertEquals((string) $memObject->value, 'another value');
    }

    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager  = new Memory\MemoryManager($this->_cache);
        $memObject      = $memoryManager->create('012345678');

        $this->assertFalse((bool) $memObject->isLocked());

        $memObject->lock();
        $this->assertTrue($memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse($memObject->isLocked());
    }
}
