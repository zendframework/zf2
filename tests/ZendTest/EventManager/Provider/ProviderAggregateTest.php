<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\EventManager\Provider;

use Zend\EventManager\Provider\ProviderAggregate;

/**
 * @group Zend_EventManager
 */
class ProviderAggregateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProviderAggregate
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new ProviderAggregate();
    }

    public function testCanAssignDefaultProviderOnConstruct()
    {
        $this->assertCount(0, $this->provider->getProviders());

        $resolver  = new ProviderAggregate(true);
        $resolvers = $resolver->getProviders();
        $this->assertCount(1, $resolvers);
        $this->assertInstanceOf('Zend\EventManager\Provider\DefaultProvider', $resolvers->top());
    }

    public function testAddProvider()
    {
        $mock = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $this->assertSame($this->provider, $this->provider->addProvider($mock));
        $this->assertCount(1, $resolvers = $this->provider->getProviders());
        $this->assertSame($mock, $resolvers->top());

        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->provider->addProvider(new \stdClass());
    }

    public function testAddMultiplesProvidersWithoutPriority()
    {
        $mock1 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $mock2 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');

        $this->assertSame($this->provider, $this->provider->addProviders(array($mock1, $mock2)));
        $this->assertCount(2, $resolvers = $this->provider->getProviders());
        $this->assertSame($mock1, $resolvers->top(), 'expecting FIFO insertion');
    }

    public function testAddMultiplesProvidersWithPriority()
    {
        $mock1 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $mock2 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');

        $this->provider->addProviders(array(
            array($mock1, 1),
            array($mock2, 2),
        ));

        $this->assertSame($mock2, $this->provider->getProviders()->top(), 'expecting Prioritized insertion');
    }

    public function testRemoveProvider()
    {
        $this->provider->addProvider($survivor = $this->getMock('Zend\EventManager\Provider\ProviderInterface'));
        $this->provider->addProvider($delete = $this->getMock('Zend\EventManager\Provider\ProviderInterface'));
        $this->provider->removeProvider($delete);

        $resolvers = $this->provider->getProviders();
        $this->assertCount(1, $resolvers);
        $this->assertSame($survivor, $resolvers->top());
    }

    public function testGetEventReturnVoidIfNoProviders()
    {
        $this->assertNull($this->provider->get('argg'));
    }

    public function testGetEventReturnVoidIfProvidersDoNotReturnAnEvent()
    {
        $resolver = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $resolver
            ->expects($this->once())
            ->method('get')
            ->with($name = 'test')
            ->will($this->returnValue(new \stdClass()));

        $this->provider->addProvider($resolver);
        $this->assertNull($this->provider->get($name));
    }

    public function testGetEventReturnFirstValidResolverResponse()
    {
        $resolverOk = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $resolverOk
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expected = $this->getMock('Zend\EventManager\EventInterface')));

        $resolverFails = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $resolverFails
            ->expects($this->never())
            ->method('get');

        $this->provider->addProvider($resolverOk, 1);
        $this->provider->addProvider($resolverFails);

        $this->assertSame($expected, $this->provider->get('test'));
    }
}
