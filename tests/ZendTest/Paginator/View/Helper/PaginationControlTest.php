<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator\View\Helper;

use Zend\Paginator;
use Zend\Paginator\View\Helper;
use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Resolver;

/**
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PaginationControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helper\PaginationControl
     */
    private $viewHelper;

    private $_paginator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $resolver = new Resolver\TemplatePathStack(array('script_paths' => array(
            __DIR__ . '/_files/scripts',
        )));
        $view = new View();
        $view->setResolver($resolver);

        $this->viewHelper = new Helper\PaginationControl();
        $this->viewHelper->setDefaultViewPartial(null);
        $this->viewHelper->setView($view);
        $adapter = new Paginator\Adapter\ArrayAdapter(range(1, 101));
        $this->_paginator = new Paginator\Paginator($adapter);
    }

    public function tearDown()
    {
        unset($this->viewHelper);
        unset($this->_paginator);
    }

    public function testGetsAndSetsView()
    {
        $view   = new View();
        $helper = new Helper\PaginationControl();
        $this->assertNull($helper->getView());
        $helper->setView($view);
        $this->assertInstanceOf('Zend\View\Renderer\RendererInterface', $helper->getView());
    }

    public function testGetsAndSetsDefaultViewPartial()
    {
        $helper = $this->viewHelper;
        $this->assertNull($helper->getDefaultViewPartial());
        $helper->setDefaultViewPartial('partial');
        $this->assertEquals('partial', $helper->getDefaultViewPartial());
        $helper->setDefaultViewPartial(null);
    }

    public function testUsesDefaultViewPartialIfNoneSupplied()
    {
        $helper = $this->viewHelper;
        $helper->setDefaultViewPartial('testPagination.phtml');
        $output = $helper->__invoke($this->_paginator);
        $this->assertContains('pagination control', $output, $output);
        $helper->setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->viewHelper->__invoke($this->_paginator);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\View\Exception\ExceptionInterface', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }

    /**
     * @group ZF-4037
     */
    public function testUsesDefaultScrollingStyleIfNoneSupplied()
    {
        $helper = $this->viewHelper;
        // First we'll make sure the base case works
        $output = $helper->__invoke($this->_paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Paginator\Paginator::setDefaultScrollingStyle('All');
        $output = $helper->__invoke($this->_paginator, null, 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        $helper->setDefaultViewPartial('testPagination.phtml');
        $output = $helper->__invoke($this->_paginator);
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }

    /**
     * @group ZF-4328
     */
    public function testUsesPaginatorFromViewOnlyIfNoneSupplied()
    {
        $this->viewHelper->getView()->vars()->paginator  = $this->_paginator;
        $paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter(range(1, 30)));
        $this->viewHelper->setDefaultViewPartial('testPagination.phtml');

        $output = $this->viewHelper->__invoke($paginator);
        $this->assertContains('page count (3)', $output, $output);
    }

    /**
     * @group ZF-4878
     */
    public function testCanUseObjectForScrollingStyle()
    {
        $all = new Paginator\ScrollingStyle\All();

        $output = $this->viewHelper->__invoke($this->_paginator, $all, 'testPagination.phtml');

        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }
}
