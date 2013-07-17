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

use Zend\View\Helper\Layout;
use Zend\View\Helper\ViewModel as ViewModelHelper;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Layout
     */
    protected $helper;

    /**
     * @var ViewModelHelper
     */
    protected $helperViewModel;

    /**
     * @var ViewModel
     */
    protected $parent;

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->renderer        = $renderer = new PhpRenderer();
        $this->helper          = $renderer->plugin('layout');
        $this->helperViewModel = $renderer->plugin('view_model');

        $this->parent = new ViewModel();
        $this->parent->getOptions()->setTemplate('layout');

        $this->helperViewModel->setRoot($this->parent);
    }

    public function testCallingSetTemplateAltersRootModelTemplate()
    {
        $this->helper->setTemplate('alternate/layout');
        $this->assertEquals('alternate/layout', $this->parent->getOptions()->getTemplate());
    }

    public function testCallingGetLayoutReturnsRootModelTemplate()
    {
        $this->assertEquals('layout', $this->helper->getLayout());
    }

    public function testCallingInvokeProxiesToSetTemplate()
    {
        $helper = $this->helper;
        $helper('alternate/layout');
        $this->assertEquals('alternate/layout', $this->parent->getOptions()->getTemplate());
    }

    public function testCallingInvokeWithNoArgumentReturnsViewModel()
    {
        $helper = $this->helper;
        $result = $helper();
        $this->assertSame($this->parent, $result);
    }

    public function testRaisesExceptionIfViewModelHelperHasNoRoot()
    {
        $renderer = new PhpRenderer();
        $helper   = $renderer->plugin('layout');

        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'view model');
        $helper->setTemplate('foo/bar');
    }
}
