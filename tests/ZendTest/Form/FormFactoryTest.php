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
use Zend\Filter;
use Zend\Form;
use Zend\Form\FormFactory;
use Zend\Form\FormManager;
use Zend\Form\Service\FormManagerConfig;
use Zend\InputFilter;
use Zend\ServiceManager\ServiceManager;

/**
 * @group Zend_Form
 */
class FormFactoryTest extends TestCase
{
    /**
     * @var FormFactory
     */
    protected $factory;

    public function setUp()
    {
        $formManager    = new FormManager(new FormManagerConfig);
        $formManager->setService('FormConfig', array());
        $formManager->setService('Config', array());
        $this->factory  = new FormFactory($formManager);
    }

    /**
     * This test should be removed when refactoring Zend\Form\FormFactory for ZF3
     */
    public function testFormElementManagerFromFormFactoryIsOverwroteByTheOneFromFormManager()
    {
        $fromFormManager = $this->factory->getFormManager()->get('ElementManager');
        $fromFormFactory = $this->factory->getFormElementManager();
        $this->assertSame($fromFormManager, $fromFormFactory);
    }

    /**
     * This test should be removed when refactoring Zend\Form\FormFactory for ZF3
     */
    public function testInputFilterFactoryFromFormFactoryIsOverwroteByTheOneFromFormManager()
    {
        $fromFormManager = $this->factory->getFormManager()->get('InputFilterFactory');
        $fromFormFactory = $this->factory->getInputFilterFactory();
        $this->assertSame($fromFormManager, $fromFormFactory);
    }
}
