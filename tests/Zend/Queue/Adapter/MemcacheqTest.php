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
 * @package    Zend_Queue
 * @subpackage UnitTests
 */

namespace ZendTest\Queue\Adapter;
use Zend\Queue\Adapter;

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @group      Zend_Queue
 */
class MemcacheqTest extends AdapterTest
{
    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You must overload this method
     *
     * @return string
     */
    public function getAdapterName()
    {
        return 'Memcacheq';
    }

    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You may overload this method.  The default return is
     * 'Zend_Queue_Adapter_' . $this->getAdapterName()
     *
     * @return string
     */
    public function getAdapterFullName()
    {
        return '\Zend\Queue\Adapter\\' . $this->getAdapterName();
    }

    public function getTestConfig()
    {
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_MEMCACHEQ_HOST')) {
            $driverOptions['host'] = TESTS_ZEND_QUEUE_MEMCACHEQ_HOST;
        }
        if (defined('TESTS_ZEND_QUEUE_MEMCACHEQ_PORT')) {
            $driverOptions['port'] = TESTS_ZEND_QUEUE_MEMCACHEQ_PORT;
        }
        return array('driverOptions' => $driverOptions);
    }

    // test the constants
    public function testConst()
    {
        /**
         * @see Zend_Queue_Adapter_Memcacheq
         */
        $this->assertTrue(is_string(Adapter\Memcacheq::DEFAULT_HOST));
        $this->assertTrue(is_integer(Adapter\Memcacheq::DEFAULT_PORT));
        $this->assertTrue(is_string(Adapter\Memcacheq::EOL));
    }
}
