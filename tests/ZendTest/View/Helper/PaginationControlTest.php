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

use Zend\Paginator;
use Zend\View\Helper\PaginationControl;
use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Resolver;

class PaginationControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaginationControl
     */
    protected $helper;

    /**
     * @var Paginator\Paginator
     */
    protected $paginator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $resolver = new Resolver\TemplatePathStack(array('script_paths' => array(
            __DIR__ . '/_files/scripts',
        )));
        $view = new View();
        $view->setResolver($resolver);

        PaginationControl::setDefaultViewPartial(null);
        $this->helper = new PaginationControl();
        $this->helper->setView($view);
        $adapter = new Paginator\Adapter\ArrayAdapter(range(1, 101));
        $this->paginator = new Paginator\Paginator($adapter);
    }

    public function tearDown()
    {
        unset($this->helper);
        unset($this->paginator);
    }

    public function testGetsAndSetsView()
    {
        $view   = new View();
        $helper = new PaginationControl();
        $this->assertNull($helper->getView());
        $helper->setView($view);
        $this->assertInstanceOf('Zend\View\Renderer\RendererInterface', $helper->getView());
    }

    public function testGetsAndSetsDefaultViewPartial()
    {
        $this->assertNull(PaginationControl::getDefaultViewPartial());
        PaginationControl::setDefaultViewPartial('partial');
        $this->assertEquals('partial', PaginationControl::getDefaultViewPartial());
        PaginationControl::setDefaultViewPartial(null);
    }

    public function testUsesDefaultViewPartialIfNoneSupplied()
    {
        PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->helper->__invoke($this->paginator);
        $this->assertContains('pagination control', $output, $output);
        PaginationControl::setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->helper->__invoke($this->paginator);
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
        // First we'll make sure the base case works
        $output = $this->helper->__invoke($this->paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Paginator\Paginator::setDefaultScrollingStyle('All');
        $output = $this->helper->__invoke($this->paginator, null, 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->helper->__invoke($this->paginator);
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testUsesPaginatorFromViewIfNoneSupplied()
    {
        $this->helper->getView()->paginator = $this->paginator;
        PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->helper->__invoke();

        $this->assertContains('pagination control', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testThrowsExceptionIfNoPaginatorFound()
    {
        PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $this->setExpectedException(
            'Zend\View\Exception\ExceptionInterface',
            'No paginator instance provided or incorrect type'
        );
        $this->helper->__invoke();
    }

    /**
     * @group ZF-4233
     */
    public function testAcceptsViewPartialInOtherModule()
    {
        try {
            $this->helper->__invoke($this->paginator, null, array('partial.phtml', 'test'));
        } catch (\Exception $e) {
            /* We don't care whether or not the module exists--we just want to
             * make sure it gets to Zend_View_Helper_Partial and it's recognized
             * as a module. */
            $this->assertInstanceOf('Zend\View\Exception\RuntimeException', $e);
            $this->assertContains('could not resolve', $e->getMessage());
        }
    }

    /**
     * @group ZF-4328
     */
    public function testUsesPaginatorFromViewOnlyIfNoneSupplied()
    {
        $this->helper->getView()->vars()->paginator  = $this->paginator;
        $paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter(range(1, 30)));
        PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->helper->__invoke($paginator);
        $this->assertContains('page count (3)', $output, $output);
    }

    /**
     * @group ZF-4878
     */
    public function testCanUseObjectForScrollingStyle()
    {
        $all = new Paginator\ScrollingStyle\All();

        $output = $this->helper->__invoke($this->paginator, $all, 'testPagination.phtml');

        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }
}
