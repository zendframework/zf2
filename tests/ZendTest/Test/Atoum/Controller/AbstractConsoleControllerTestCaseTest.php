<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace ZendTest\Test\Atoum\Controller;

use Zend\Test\Atoum\Controller\AbstractConsoleControllerTestCase;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class AbstractConsoleControllerTestCaseTest extends AbstractConsoleControllerTestCase
{
    public function beforeTestMethod($method)
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::beforeTestMethod($method);
    }

    public function testUseOfRouter()
    {
        $this->boolean($this->getUseConsoleRequest())->isEqualTo(true);
    }

    public function testApplicationClass()
    {
        $this->object($this->getApplication())
                ->isInstanceOf('\Zend\Mvc\Application');
    }
    
    public function testApplicationServiceLocatorClass()
    {
        $this->object($this->getApplicationServiceLocator())
                ->isInstanceOf('Zend\ServiceManager\ServiceManager');
    }
    
    public function testAssertResponseStatusCode()
    {
        $this->dispatch('--console');
        $this->assertResponseStatusCode(0);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertResponseStatusCode(1); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotResponseStatusCode()
    {
        $this->dispatch('--console');
        $this->assertNotResponseStatusCode(1);

        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotResponseStatusCode(0); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertResponseStatusCodeWithBadCode()
    {
        $this->dispatch('--console');
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertResponseStatusCode(2); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotResponseStatusCodeWithBadCode()
    {
        $this->dispatch('--console');
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotResponseStatusCode(2); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }
}
