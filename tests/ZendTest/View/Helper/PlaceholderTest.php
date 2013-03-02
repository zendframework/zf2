<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper\Placeholder;
use Zend\View\Helper\Placeholder\Registry as PlaceholderRegistry;
use Zend\View\Renderer\PhpRenderer as View;

class PlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Placeholder
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
        PlaceholderRegistry::unsetRegistry();

        $this->helper = new Placeholder();
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
        PlaceholderRegistry::unsetRegistry();
    }

    public function testMultiplePlaceholdersUseSameRegistry()
    {
        $placeholder = new Placeholder();
        $this->assertSame($this->helper->getRegistry(), $placeholder->getRegistry());
    }

    /**
     * @return void
     */
    public function testSetView()
    {
        $view = new View();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->getView());
    }

    /**
     * @return void
     */
    public function testPlaceholderRetrievesContainer()
    {
        $container = $this->helper->__invoke('foo');
        $this->assertInstanceOf('Zend\View\Helper\Placeholder\Container\AbstractContainer', $container);
    }

    /**
     * @return void
     */
    public function testPlaceholderRetrievesSameContainerOnSubsequentCalls()
    {
        $container1 = $this->helper->__invoke('foo');
        $container2 = $this->helper->__invoke('foo');
        $this->assertSame($container1, $container2);
    }
}
