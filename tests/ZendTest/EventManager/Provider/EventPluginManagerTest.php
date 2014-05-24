<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\EventManager\Provider;

use Zend\EventManager\Provider\EventPluginManager;

/**
 * @group Zend_EventManager
 */
class EventPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventPluginManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new EventPluginManager;
    }

    public function testIsNotSharedByDefault()
    {
        $this->assertFalse($this->manager->shareByDefault());
    }

    public function testValidatePlugin()
    {
        $this->assertNull($this->manager->validatePlugin($this->getMock('Zend\EventManager\EventInterface')));

        $this->setExpectedException('Zend\EventManager\Exception\RuntimeException');
        $this->manager->validatePlugin(new \stdClass);
    }
}
 