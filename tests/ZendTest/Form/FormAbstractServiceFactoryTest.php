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

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

class FormAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceManager;

    /**
     * Set up service manager and form specification.
     */
    protected function setUp ()
    {
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(array(
            'abstract_factories' => array(
                'Zend\Form\FormAbstractServiceFactory'
            )
        )));
        $this->serviceManager->setService('Config', array(
            'form' => array(
                'Frontend\Form\FooBar' => array(
                    'object' => 'foobar',
                    'attributes' => array(
                        'action' => '/path/to/controller',
                        'method' => 'POST'
                    ),
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'foo',
                                'type' => 'text',
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'bar',
                                'type' => 'text',
                            ),
                        ),
                        array(
                            'spec' => array(
                                'name' => 'foobar',
                                'type' => 'foobar',
                            ),
                        ),
                    ),
                ),

                'element_manager' => array(
                    'invokables' => array(
                        'foobar' => 'ZendTest\Form\TestAsset\ElementCustomTextfield',
                    ),
                ),

                'object_manager' => array(
                    'invokables' => array(
                        'foobar' => 'ZendTest\Form\TestAsset\Model',
                    ),
                ),
            ),
        ));
    }

    /**
     *
     * @return array
     */
    public function providerValidService ()
    {
        return array(
            array(
                'Frontend\Form\FooBar',
                'ZendTest\Form\TestAsset\Model',
            )
        );
    }

    /**
     *
     * @return array
     */
    public function providerInvalidService ()
    {
        return array(
            array(
                'Frontend\Form\Unkonwn'
            )
        );
    }

    /**
     *
     * @param string $service
     * @dataProvider providerValidService
     */
    public function testValidService ($service)
    {
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf('Zend\Form\Form', $actual);
    }

    /**
     *
     * @param string $service
     * @dataProvider providerInvalidService
     * @expectedException Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testInvalidService ($service)
    {
        $this->serviceManager->get($service);
    }

    /**
     * @param string $service
     * @dataProvider providerValidService
     */
    public function testObjectBinding ($service, $classname)
    {
        /* @var $form \Zend\Form\Form */
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf($classname, $actual->getObject());
    }

    /**
     * @param string $service
     * @dataProvider providerValidService
     */
    public function testValuesBindind ($service, $classname)
    {
        /* @var $form \Zend\Form\Form */
        $form = $this->serviceManager->get($service);
        $form->bindValues(array(
            'foo' => 'foo',
            'bar' => 'bar',
            'foobar' => 'foobar',
        ));

        $this->assertEquals('foo', $form->getObject()->foo);
        $this->assertEquals('bar', $form->getObject()->bar);
        $this->assertEquals('foobar', $form->getObject()->foobar);
    }

    /**
     * @param string $service
     * @dataProvider providerValidService
     */
    public function testElementBindind ($service, $classname)
    {
        /* @var $form \Zend\Form\Form */
        $form = $this->serviceManager->get($service);
        $form->bindValues(array(
            'foo' => 'foo',
            'bar' => 'bar',
            'foobar' => 'foobar',
        ));

        $this->assertEquals('foo', $form->get('foo')->getValue());
        $this->assertEquals('bar', $form->get('bar')->getValue());
        $this->assertEquals('foobar', $form->get('foobar')->getValue());
    }

    /**
     *
     * @param string $service
     * @dataProvider providerValidService
     */
    public function testCustomElement ($service)
    {
        $form = $this->serviceManager->get($service);
        $this->assertInstanceOf('ZendTest\Form\TestAsset\ElementCustomTextfield', $form->get('foobar'));
    }
}
