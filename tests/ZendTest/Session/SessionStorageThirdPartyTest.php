<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session;

use Zend\Session\Storage\SessionStorage;
use Zend\Session\Storage\ArrayStorage;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class SessionStorageThirdPartyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionStorage
     */
    protected $storage;

    public function setUp()
    {
        $_SESSION = array();
        $this->storage = new SessionStorage();
    }

    public function tearDown()
    {
        $_SESSION = array();
    }

    public function testAssignment()
    {
        $_SESSION['foo'] = 'bar';
        $this->assertEquals('bar', $this->storage['foo']);
    }

    public function testMultiDimensionalAssignment()
    {
        $_SESSION['foo']['bar'] = 'baz';
        $this->assertEquals('baz', $this->storage['foo']['bar']);
    }

    public function testUnset()
    {
        $_SESSION['foo'] = 'bar';
        unset($_SESSION['foo']);
        $this->assertFalse(isset($this->storage['foo']));
    }

    public function testMultiDimensionalUnset()
    {
        $_SESSION['foo']['bar'] = 'baz';
        unset($_SESSION['foo']['bar']);
        $this->assertFalse(isset($this->storage['foo']['bar']));
    }
}
