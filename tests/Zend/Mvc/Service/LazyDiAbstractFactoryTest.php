<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Mvc\Service\LazyDiAbstractFactory;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class LazyDiAbstractFactoryTest extends TestCase
{
    public function testCreateServiceWithName()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Di'))
            ->will($this->returnValue($this->getMock('Zend\Di\Di')));
        $serviceLocator
            ->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));
        $factory = new LazyDiAbstractFactory();
        $instance = $factory->createServiceWithName($serviceLocator, 'stdClass', 'stdClass');
        $this->assertInstanceOf('stdClass', $instance);
    }

    public function testCanCreateServiceWithName()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Di'))
            ->will($this->returnValue($this->getMock('Zend\Di\Di')));
        $factory = new LazyDiAbstractFactory();
        $this->assertFalse(
            $factory->canCreateServiceWithName(
                $serviceLocator,
                __NAMESPACE__ . '\Non\Existing',
                __NAMESPACE__ . '\Non\Existing'
            )
        );
    }
}
