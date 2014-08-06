<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\EventManager\Provider;

use Zend\EventManager\Provider\DefaultProvider;

/**
 * @group Zend_EventManager
 */
class DefaultProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultProvider
     */
    public $provider;

    public function setUp()
    {
        $this->provider = new DefaultProvider;
    }

    public function testGetEventReturnInstanceOfZendEventByDefault()
    {
        $this->assertInstanceOf('Zend\EventManager\Event', $event = $this->provider->get('test'));
        $this->assertEquals('test', $event->getName());
        $this->assertNotSame($event, $this->provider->get('test'));
    }

    public function testSetEventClass()
    {
        $this->provider->setEventClass($class = 'ZendTest\EventManager\TestAsset\CustomEvent');
        $this->assertInstanceOf($class, $this->provider->get('test'));
    }
}
