<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\BlackHole;
use Zend\Cache\StorageFactory;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class BlackHoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The storage adapter
     *
     * @var StorageInterface
     */
    protected $storage;

    public function setUp()
    {
        $this->storage = StorageFactory::adapterFactory('BlackHole');
    }

    public function testGetOptions()
    {
        $options = $this->storage->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\AdapterOptions', $options);
    }

    public function testSetOptions()
    {
        $this->storage->setOptions(['namespace' => 'test']);
        $this->assertSame('test', $this->storage->getOptions()->getNamespace());
    }

    public function testGetCapabilities()
    {
        $capabilities = $this->storage->getCapabilities();
        $this->assertInstanceOf('Zend\Cache\Storage\Capabilities', $capabilities);
    }

    public function testSingleStorageOperatios()
    {
        $this->assertFalse($this->storage->setItem('test', 1));
        $this->assertFalse($this->storage->addItem('test', 1));
        $this->assertFalse($this->storage->replaceItem('test', 1));
        $this->assertFalse($this->storage->touchItem('test'));
        $this->assertFalse($this->storage->incrementItem('test', 1));
        $this->assertFalse($this->storage->decrementItem('test', 1));
        $this->assertFalse($this->storage->hasItem('test'));
        $this->assertNull($this->storage->getItem('test', $success));
        $this->assertFalse($success);
        $this->assertFalse($this->storage->getMetadata('test'));
        $this->assertFalse($this->storage->removeItem('test'));
    }

    public function testMultiStorageOperatios()
    {
        $this->assertSame(['test'], $this->storage->setItems(['test' => 1]));
        $this->assertSame(['test'], $this->storage->addItems(['test' => 1]));
        $this->assertSame(['test'], $this->storage->replaceItems(['test' => 1]));
        $this->assertSame(['test'], $this->storage->touchItems(['test']));
        $this->assertSame([], $this->storage->incrementItems(['test' => 1]));
        $this->assertSame([], $this->storage->decrementItems(['test' => 1]));
        $this->assertSame([], $this->storage->hasItems(['test']));
        $this->assertSame([], $this->storage->getItems(['test']));
        $this->assertSame([], $this->storage->getMetadatas(['test']));
        $this->assertSame(['test'], $this->storage->removeItems(['test']));
    }

    public function testAvailableSpaceCapableInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\AvailableSpaceCapableInterface', $this->storage);
        $this->assertSame(0, $this->storage->getAvailableSpace());
    }

    public function testClearByNamespaceInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\ClearByNamespaceInterface', $this->storage);
        $this->assertFalse($this->storage->clearByNamespace('test'));
    }

    public function testClearByPrefixInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\ClearByPrefixInterface', $this->storage);
        $this->assertFalse($this->storage->clearByPrefix('test'));
    }

    public function testCleariExpiredInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\ClearExpiredInterface', $this->storage);
        $this->assertFalse($this->storage->clearExpired());
    }

    public function testFlushableInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\FlushableInterface', $this->storage);
        $this->assertFalse($this->storage->flush());
    }

    public function testIterableInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\IterableInterface', $this->storage);
        $iterator = $this->storage->getIterator();
        foreach ($iterator as $item) {
            $this->fail('The iterator of the BlackHole adapter should be empty');
        }
    }

    public function testOptimizableInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\OptimizableInterface', $this->storage);
        $this->assertFalse($this->storage->optimize());
    }

    public function testTaggableInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\TaggableInterface', $this->storage);
        $this->assertFalse($this->storage->setTags('test', ['tag1']));
        $this->assertFalse($this->storage->getTags('test'));
        $this->assertFalse($this->storage->clearByTags(['tag1']));
    }

    public function testTotalSpaceCapableInterface()
    {
        $this->assertInstanceOf('Zend\Cache\Storage\TotalSpaceCapableInterface', $this->storage);
        $this->assertSame(0, $this->storage->getTotalSpace());
    }
}
