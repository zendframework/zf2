<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\FormFactory;
use Zend\Form\FormManager;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @group Zend_Form
 */
class FormManagerTest extends TestCase
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var FormManager
     */
    protected $formManager;

    public function setUp()
    {
        $this->formManager = new FormManager;
        $this->formManager->setServiceLocator(new ServiceManager);
    }

    public function testFormManagerInjectsItselfIntoFormFactoryOnLazyInstantiation()
    {
        $formManager = $this->formManager;
        // Lazy loads a FormFactory instance
        $formFactory = $formManager->getFormFactory();
        $this->assertSame($formManager, $formFactory->getFormManager());
    }

    public function testFormManagerInjectsItselfIntoFormFactoryOnSetting()
    {
        $formManager = $this->formManager;
        $formFactory = new FormFactory;
        $formManager->setFormFactory($formFactory);
        $this->assertSame($formManager, $formFactory->getFormManager());
    }
}
