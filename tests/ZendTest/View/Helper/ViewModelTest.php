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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\ViewModel as ViewModelHelper;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ViewModelTest extends TestCase
{
    /**
     * @var ViewModelHelper
     */
    protected $helper;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->helper = new ViewModelHelper();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->helper);
    }

    public function testCurrentIsNullByDefault()
    {
        $this->assertNull($this->helper->getCurrent());
    }

    public function testCurrentIsMutable()
    {
        $helper = $this->helper;

        $helper->setCurrent(new JsonModel);
        $this->assertInstanceOf('Zend\View\Model\JsonModel', $helper->getCurrent());

        $helper->setCurrent(new ViewModel);
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $helper->getCurrent());
    }

    public function testRootIsNullByDefault()
    {
        $this->assertNull($this->helper->getRoot());
    }

    public function testRootIsMutable()
    {
        $helper = $this->helper;

        $helper->setRoot(new JsonModel);
        $this->assertInstanceOf('Zend\View\Model\JsonModel', $helper->getRoot());

        $helper->setRoot(new ViewModel);
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $helper->getRoot());
    }

    public function testVerifyHasCurrentFunction()
    {
        $helper = $this->helper;

        $this->assertFalse($helper->hasCurrent());

        $helper->setCurrent(new ViewModel());
        $this->assertTrue($helper->hasCurrent());
    }

    public function testVerifyHasRootFunction()
    {
        $helper = $this->helper;

        $this->assertFalse($helper->hasRoot());

        $helper->setRoot(new ViewModel());
        $this->assertTrue($helper->hasRoot());
    }
}
