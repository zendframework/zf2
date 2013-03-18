<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper\Placeholder;

use Zend\View\Helper\Placeholder\Container;
use Zend\View\Helper\Placeholder\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    protected $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::unsetRegistry();
        $this->helper = new Registry();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    /**
     * @return void
     */
    public function testCreateContainer()
    {
        $this->assertFalse($this->helper->containerExists('foo'));
        $this->helper->createContainer('foo');
        $this->assertTrue($this->helper->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testCreateContainerCreatesDefaultContainerClass()
    {
        $this->assertFalse($this->helper->containerExists('foo'));
        $container = $this->helper->createContainer('foo');
        $this->assertTrue($container instanceof Container);
    }

    /**
     * @return void
     */
    public function testGetContainerCreatesContainerIfNonExistent()
    {
        $this->assertFalse($this->helper->containerExists('foo'));
        $container = $this->helper->getContainer('foo');
        $this->assertTrue($container instanceof Container\AbstractContainer);
        $this->assertTrue($this->helper->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testSetContainerCreatesRegistryEntry()
    {
        $foo = new Container(array('foo', 'bar'));
        $this->assertFalse($this->helper->containerExists('foo'));
        $this->helper->setContainer('foo', $foo);
        $this->assertTrue($this->helper->containerExists('foo'));
    }

    public function testSetContainerCreatesRegistersContainerInstance()
    {
        $foo = new Container(array('foo', 'bar'));
        $this->assertFalse($this->helper->containerExists('foo'));
        $this->helper->setContainer('foo', $foo);
        $container = $this->helper->getContainer('foo');
        $this->assertSame($foo, $container);
    }

    public function testContainerClassAccessorsSetState()
    {
        $this->assertEquals('Zend\View\Helper\Placeholder\Container', $this->helper->getContainerClass());
        $this->helper->setContainerClass('ZendTest\View\Helper\Placeholder\MockContainer');
        $this->assertEquals('ZendTest\View\Helper\Placeholder\MockContainer', $this->helper->getContainerClass());
    }

    public function testSetContainerClassThrowsExceptionWithInvalidContainerClass()
    {
        try {
            $this->helper->setContainerClass('ZendTest\View\Helper\Placeholder\BogusContainer');
            $this->fail('Invalid container classes should not be accepted');
        } catch (\Exception $e) {
        }
    }

    public function testDeletingContainerRemovesFromRegistry()
    {
        $this->helper->createContainer('foo');
        $this->assertTrue($this->helper->containerExists('foo'));
        $result = $this->helper->deleteContainer('foo');
        $this->assertFalse($this->helper->containerExists('foo'));
        $this->assertTrue($result);
    }

    public function testDeleteContainerReturnsFalseIfContainerDoesNotExist()
    {
        $result = $this->helper->deleteContainer('foo');
        $this->assertFalse($result);
    }

    public function testUsingCustomContainerClassCreatesContainersOfCustomClass()
    {
        $this->helper->setContainerClass('ZendTest\View\Helper\Placeholder\MockContainer');
        $container = $this->helper->createContainer('foo');
        $this->assertTrue($container instanceof MockContainer);
    }

    public function testGetRegistryReturnsRegistryInstance()
    {
        $registry = Registry::getRegistry();
        $this->assertTrue($registry instanceof Registry);
    }

    public function testGetRegistrySubsequentTimesReturnsSameInstance()
    {
        $registry1 = Registry::getRegistry();
        $registry2 = Registry::getRegistry();
        $this->assertSame($registry1, $registry2);
    }

    /**
     * @group ZF-10793
     */
    public function testSetValueCreateContainer()
    {
        $this->helper->setContainerClass('ZendTest\View\Helper\Placeholder\MockContainer');
        $data = array(
            'ZF-10793'
        );
        $container = $this->helper->createContainer('foo', $data);
        $this->assertEquals(array('ZF-10793'), $container->data);
    }
}

class MockContainer extends Container\AbstractContainer
{
    public $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }
}

class BogusContainer
{
}
