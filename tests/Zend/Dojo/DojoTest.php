<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo;

use Zend\Form\Form;
use Zend\Form\SubForm;
use Zend\View;

/**
 * Test class for Zend_Dojo
 *
 * @category   Zend
 * @package    Zend_Date
 * @subpackage UnitTests
 * @group      Zend_Dojo
 */
class DojoTest extends \PHPUnit_Framework_TestCase
{
    public function getForm()
    {
        $form = new Form();
        $form->addElement('text', 'foo')
             ->addElement('text', 'bar')
             ->addElement('text', 'baz')
             ->addElement('text', 'bat');
        $subForm = new SubForm();
        $subForm->addElement('text', 'foo')
                ->addElement('text', 'bar')
                ->addElement('text', 'baz')
                ->addElement('text', 'bat');
        $form->addDisplayGroup(array('foo', 'bar'), 'foobar')
             ->addSubForm($subForm, 'sub')
             ->setView(new View\Renderer\PhpRenderer);
        return $form;
    }

    public function testEnableFormShouldSetAppropriateDecoratorAndElementPaths()
    {
        $form = $this->getForm();
        \Zend\Dojo\Dojo::enableForm($form);

        $decPluginLoader = $form->getPluginLoader('decorator');
        $paths = $decPluginLoader->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);

        $elPluginLoader = $form->getPluginLoader('element');
        $paths = $elPluginLoader->getPaths('Zend\Dojo\Form\Element');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);

        $decPluginLoader = $form->baz->getPluginLoader('decorator');
        $paths = $decPluginLoader->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);

        $decPluginLoader = $form->foobar->getPluginLoader();
        $paths = $decPluginLoader->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);

        $decPluginLoader = $form->sub->getPluginLoader('decorator');
        $paths = $decPluginLoader->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);

        $elPluginLoader = $form->sub->getPluginLoader('element');
        $paths = $elPluginLoader->getPaths('Zend\Dojo\Form\Element');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);
    }

    public function testEnableFormShouldSetAppropriateDefaultDisplayGroup()
    {
        $form = $this->getForm();
        \Zend\Dojo\Dojo::enableForm($form);
        $this->assertEquals('Zend\Dojo\Form\DisplayGroup', $form->getDefaultDisplayGroupClass());
    }

    public function testEnableFormShouldSetAppropriateViewHelperPaths()
    {
        $form = $this->getForm();
        \Zend\Dojo\Dojo::enableForm($form);
        $view = $form->getView();

        $this->assertInstanceOf('Zend\Dojo\View\Helper\Dojo', $view->plugin('dojo'));
    }

    public function testEnableViewShouldRegisterDojoViewHelpers()
    {
        $view = new View\Renderer\PhpRenderer;
        \Zend\Dojo\Dojo::enableView($view);

        $this->assertInstanceOf('Zend\Dojo\View\Helper\Dojo', $view->plugin('dojo'));
    }

    public function testDisableViewShouldUnregisterDojoViewHelpers()
    {
        $view = new View\Renderer\PhpRenderer;
        \Zend\Dojo\Dojo::enableView($view);

        $this->assertInstanceOf('Zend\Dojo\View\Helper\Dojo', $view->plugin('dojo'));

        \Zend\Dojo\Dojo::disableView($view);

        try {
            $view->plugin('dojo');
            $this->fail('RuntimeException was expected but not thrown');
        } catch(\Zend\Loader\Exception\RuntimeException $e) {
            // success
        }
    }
}
