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
    protected $resolver;

    public function setUp()
    {
        $this->resolver = new ProviderAggregate();
    }

    public function testCanAssignDefaultProviderOnConstruct()
    {
        $this->assertCount(0, $this->resolver->getResolvers());

        $resolver  = new ProviderAggregate(true);
        $resolvers = $resolver->getResolvers();
        $this->assertCount(1, $resolvers);
        $this->assertInstanceOf('Zend\EventManager\Provider\DefaultProvider', $resolvers->top());
    }

    public function testAddResolver()
    {
        $mock = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $this->assertSame($this->resolver, $this->resolver->addProvider($mock));
        $this->assertCount(1, $resolvers = $this->resolver->getResolvers());
        $this->assertSame($mock, $resolvers->top());

        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->resolver->addProvider(new \stdClass());
    }

    public function testAddMultiplesResolversWithoutPriority()
    {
        $mock1 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $mock2 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');

        $this->resolver->addProviders([$mock1, $mock2]);
        $this->assertCount(2, $resolvers = $this->resolver->getResolvers());
        $this->assertSame($mock1, $resolvers->top(), 'expecting FIFO insertion');
    }

    public function testAddMultiplesResolversWithPriority()
    {
        $mock1 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $mock2 = $this->getMock('Zend\EventManager\Provider\ProviderInterface');

        $this->resolver->addProviders([
            [$mock1, 1],
            [$mock2, 2],
        ]);

        $this->assertSame($mock2, $this->resolver->getResolvers()->top(), 'expecting Prioritized insertion');
    }

    public function testRemoveResolver()
    {
        $this->resolver->addProvider($survivor = $this->getMock('Zend\EventManager\Provider\ProviderInterface'));
        $this->resolver->addProvider($delete = $this->getMock('Zend\EventManager\Provider\ProviderInterface'));
        $this->resolver->removeProvider($delete);

        $resolvers = $this->resolver->getResolvers();
        $this->assertCount(1, $resolvers);
        $this->assertSame($survivor, $resolvers->top());
    }

    public function testGetEventReturnVoidIfNoProviders()
    {
        $this->assertNull($this->resolver->get('argg'));
    }

    public function testGetEventReturnVoidIfProvidersDoNotReturnAnEvent()
    {
        $resolver = $this->getMock('Zend\EventManager\Provider\ProviderInterface');
        $resolver
            ->expects($this->once())
            ->method('get')
            ->with($name = 'test')
            ->will($this->returnValue(new \stdClass()));

        $this->resolver->addProvider($resolver);
        $this->assertNull($this->resolver->get($name));
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

        $this->resolver->addProvider($resolverOk, 1);
        $this->resolver->addProvider($resolverFails);

        $this->assertSame($expected, $this->resolver->get('test'));
    }
}
