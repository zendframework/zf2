<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\NoCache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class NoCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that an item is not stored.
     *
     * @return void
     */
    public function testHasItemIsFalseAfterSetItem()
    {
        $cache = new NoCache();
        $cache->setItem('key', 'value');
        $this->assertFalse((boolean)$cache->hasItem('key'));
    }
}
