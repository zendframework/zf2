<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\ServiceManager\ServiceManager;

/**
 * @requires PHP 5.4
 * @group    Zend_ServiceManager
 */
class ServiceManagerAwareTraitTest extends TestCase
{
    public function testSetServiceManager()
    {
        $object = $this->getObjectForTrait('\Zend\ServiceManager\ServiceManagerAwareTrait');

        $this->assertAttributeEquals(null, 'serviceManager', $object);

        $serviceManager = new ServiceManager;

        $object->setServiceManager($serviceManager);

        $this->assertAttributeEquals($serviceManager, 'serviceManager', $object);
    }
}
