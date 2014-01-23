<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Authentication\Storage;

use Zend\Authentication\Storage\Cache;

use PHPUnit_Framework_TestCase as TestCase;

/**
  * @group      Zend_Auth
 */
class CacheTest extends TestCase
{
    public function setUp()
    {
        $this->cache = new Cache(new \Zend\Cache\Storage\Adapter\Memory(), 'Test_Namespace', 'Test_Key');
    }

    /**
     * Ensure cache without storage behaves as empty storage.
     */
    public function testEmptyCache()
    {
        $this->assertTrue($this->cache->isEmpty());
    }

    /**
     * Ensure writing and reading to cache work correctly.
     */
    public function testReadAndWrite()
    {
        $this->cache->write('Test_Data');

        $this->assertEquals($this->cache->read(), 'Test_Data');
        $this->assertFalse($this->cache->isEmpty());
    }

    /**
     * Ensure the clear() method correctly clears the cache storage.
     */
    public function testClear()
    {
        $this->cache->write('Test_Data');
        $this->cache->clear();
        $this->assertTrue($this->cache->isEmpty());
    }

    /**
     * Ensure that getKey() method returns key supplied at instantiation.
     */
    public function testGetKey()
    {
        $this->assertEquals($this->cache->getKey(), 'Test_Key');
    }

    /**
     * Ensure that the cache namespace can be set and retrieved correctly.
     */
    public function testNamespace()
    {
        $this->assertEquals($this->cache->getNamespace(), 'Test_Namespace');
        $this->cache->setNamespace('New_Test_Namespace');
        $this->assertEquals($this->cache->getNamespace(), 'New_Test_Namespace');
    }
}
