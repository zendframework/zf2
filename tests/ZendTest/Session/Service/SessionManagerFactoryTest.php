<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Service\SessionManagerFactory;
use Zend\Session\Storage\ArrayStorage;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class SessionManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->services->setFactory('Zend\Session\ManagerInterface', 'Zend\Session\Service\SessionManagerFactory');
    }

    public function testCreatesSessionManager()
    {
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $this->assertInstanceOf('Zend\Session\SessionManager', $manager);
    }

    public function testConfigObjectIsInjectedIfPresentInServices()
    {
        $config = $this->getMock('Zend\Session\Config\ConfigInterface');
        $this->services->setService('Zend\Session\Config\ConfigInterface', $config);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $test = $manager->getConfig();
        $this->assertSame($config, $test);
    }

    public function testFactoryWillInjectStorageIfPresentInServices()
    {
        // Using concrete version here as mocking was too complex
        $storage = new ArrayStorage();
        $this->services->setService('Zend\Session\Storage\StorageInterface', $storage);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $test = $manager->getStorage();
        $this->assertSame($storage, $test);
    }

    public function testFactoryWillInjectSaveHandlerIfPresentInServices()
    {
        $saveHandler = $this->getMock('Zend\Session\SaveHandler\SaveHandlerInterface');
        $this->services->setService('Zend\Session\SaveHandler\SaveHandlerInterface', $saveHandler);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $test = $manager->getSaveHandler();
        $this->assertSame($saveHandler, $test);
    }
}
