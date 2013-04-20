<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\FormFactory;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\Form\FormManager;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @group Zend_Form
 */
class FormElementManagerTest extends TestCase
{
    /**
     * @var FormElementManager
     */
    protected $manager;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var FormManager
     */
    protected $formManager;

    public function setUp()
    {
        $this->manager        = new FormElementManager();
        $this->serviceLocator = new ServiceManager;
        $this->formManager    = new FormManager;
        // The service manager must have a 'FormManager' service registered
        // to be able to inject it into any element implementing FormManagerAwareInterface
        $this->serviceLocator->setService('FormManager', $this->formManager);
    }

    public function testInjectToFormFactoryAware()
    {
        $form = $this->manager->get('Form');
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    public function testInjectToFormManagerAware()
    {
        $this->manager->setServiceLocator($this->serviceLocator);

        $form = $this->manager->get('Form');
        $this->assertSame($this->formManager, $form->getFormManager());
    }

    /**
     * @group 3735
     */
    public function testInjectsFormElementManagerToFormComposedByFormFactoryAwareElement()
    {
        $factory = new FormFactory();
        $this->manager->setFactory('my-form', function ($elements) use ($factory) {
            $form = new Form();
            $form->setFormFactory($factory);
            return $form;
        });
        $form = $this->manager->get('my-Form');
        $this->assertSame($factory, $form->getFormFactory());
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidElementException');
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\Form\Exception\InvalidElementException');
        $this->manager->get('test');
    }

    /**
     * Tests if an element implementing FormManagerAwareInterface and FormFactoryAwareInterface
     * returns the same instance of a factory via $element->getFormManager()->getFormFactory() and
     * $element->getFormFactory()
     */
    public function testElementFormFactoryAndFormManagerAwareShareTheSameInstanceOfFormFactoryObject()
    {
        $element = $this->manager->get('form');
        $this->assertSame($element->getFormManager()->getFormFactory(), $element->getFormFactory());
    }

    /**
     * Tests if an element implementing FormManagerAwareInterface and FormFactoryAwareInterface
     * returns the same instance of a form element manager via $element->getFormManager()->getFormElementManager()
     * and $element->getFormElementManager()
     */
    public function testElementFormFactoryAndFormManagerAwareShareTheSameInstanceOfFormElementManagerObject()
    {
        $this->manager->setServiceLocator($this->serviceLocator);

        $element         = $this->manager->get('form');
        $fromFormManager = $element->getFormManager()->getFormElementManager();
        $fromFormFactory = $element->getFormFactory()->getFormElementManager();
        $this->assertSame($fromFormManager, $fromFormFactory);
    }
}
