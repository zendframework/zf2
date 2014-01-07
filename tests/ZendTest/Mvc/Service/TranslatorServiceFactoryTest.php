<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\TranslatorServiceFactory;
use Zend\ServiceManager\ServiceManager;

class TranslatorServiceFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->factory = new TranslatorServiceFactory();
        $this->services = new ServiceManager();
    }

    public function testReturnsMvcTranslatorWithTranslatorInterfaceServiceComposedWhenPresent()
    {
        $i18nTranslator = $this->getMock('Zend\I18n\Translator\TranslatorInterface');
        $this->services->setService('Zend\I18n\Translator\TranslatorInterface', $i18nTranslator);

        $translator = $this->factory->createService($this->services);
        $this->assertInstanceOf('Zend\Mvc\I18n\Translator', $translator);
        $this->assertSame($i18nTranslator, $translator->getTranslator());
    }

    public function testReturnsMvcTranslatorWithDummyTranslatorComposedWhenNoTranslatorInterfaceOrConfigServicesPresent()
    {
        $translator = $this->factory->createService($this->services);
        $this->assertInstanceOf('Zend\Mvc\I18n\Translator', $translator);
        $this->assertInstanceOf('Zend\Mvc\I18n\DummyTranslator', $translator->getTranslator());
    }

    public function testReturnsTranslatorBasedOnConfigurationWhenNoTranslatorInterfaceServicePresent()
    {
        $config = array('translator' => array(
            'locale' => 'en_US',
        ));
        $this->services->setService('Config', $config);

        $translator = $this->factory->createService($this->services);
        $this->assertInstanceOf('Zend\Mvc\I18n\Translator', $translator);
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $translator->getTranslator());

        return array(
            'translator' => $translator->getTranslator(),
            'services'   => $this->services,
        );
    }

    /**
     * @depends testReturnsTranslatorBasedOnConfigurationWhenNoTranslatorInterfaceServicePresent
     */
    public function testSetsInstantiatedI18nTranslatorInstanceInServiceManager($dependencies)
    {
        $translator = $dependencies['translator'];
        $services   = $dependencies['services'];
        $this->assertTrue($services->has('Zend\I18n\Translator\TranslatorInterface'));
        $this->assertSame($translator, $services->get('Zend\I18n\Translator\TranslatorInterface'));
    }

    public function testPrefersTranslatorInterfaceImplementationOverConfig()
    {
        $config = array('translator' => array(
            'locale' => 'en_US',
        ));
        $this->services->setService('Config', $config);

        $i18nTranslator = $this->getMock('Zend\I18n\Translator\TranslatorInterface');
        $this->services->setService('Zend\I18n\Translator\TranslatorInterface', $i18nTranslator);

        $translator = $this->factory->createService($this->services);
        $this->assertInstanceOf('Zend\Mvc\I18n\Translator', $translator);
        $this->assertSame($i18nTranslator, $translator->getTranslator());
    }
}
