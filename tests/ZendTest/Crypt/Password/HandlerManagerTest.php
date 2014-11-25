<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Password;

use Zend\Crypt\Password\HandlerManager;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Zend\Crypt\Password\HandlerManager
 */
class HandlerManagerTest extends TestCase
{
    public function testCreateSimpleMd5()
    {
        $manager = new HandlerManager();
        $this->assertInstanceOf('Zend\Crypt\Password\Algorithm\SimpleMd5', $manager->get('SimpleMd5'));
    }

    public function testCreateSimpleSha1()
    {
        $manager = new HandlerManager();
        $this->assertInstanceOf('Zend\Crypt\Password\Algorithm\SimpleSha1', $manager->get('SimpleSha1'));
    }

    public function testCreateBcrypt()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Zend\Crypt\Config', array());
        $manager = new HandlerManager();
        $manager->setServiceLocator($serviceManager);
        $this->assertInstanceOf('Zend\Crypt\Password\Algorithm\Bcrypt', $manager->get('Bcrypt'));
    }

    public function testCreateInvalidHandler()
    {
        $this->setExpectedException(
            'Zend\Crypt\Exception\RuntimeException',
            'Plugin of type stdClass is invalid; must implement Zend\Crypt\Password\HandlerInterface'
        );

        $manager = new HandlerManager();
        $manager->setService('invalid', new stdClass());
    }
}
