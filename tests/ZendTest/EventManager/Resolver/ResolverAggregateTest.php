<?php

namespace ZendTest\EventManager\Resolver;

use Zend\EventManager\Resolver\ResolverAggregate;

/**
 * @group Zend_EventManager
 */
class ResolverAggregateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResolverAggregate
     */
    protected $resolver;

    public function setUp()
    {
        $this->resolver = new ResolverAggregate();
    }

    public function testCanAssignDefaultResolverOnConstruct()
    {
        $this->assertCount(0, $this->resolver->getResolvers());

        $resolver  = new ResolverAggregate(true);
        $resolvers = $resolver->getResolvers();
        $this->assertCount(1, $resolvers);
        $this->assertInstanceOf('Zend\EventManager\Resolver\DefaultResolver', $resolvers->top());
    }

    public function testAddResolver()
    {
        $mock = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');
        $this->assertSame($this->resolver, $this->resolver->addResolver($mock));
        $this->assertCount(1, $resolvers = $this->resolver->getResolvers());
        $this->assertSame($mock, $resolvers->top());

        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->resolver->addResolver(new \stdClass());
    }

    public function testAddMultiplesResolversWithoutPriority()
    {
        $mock1 = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');
        $mock2 = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');

        $this->resolver->addResolvers([$mock1, $mock2]);
        $this->assertCount(2, $resolvers = $this->resolver->getResolvers());
        $this->assertSame($mock1, $resolvers->top(), 'expecting FIFO insertion');
    }

    public function testAddMultiplesResolversWithPriority()
    {
        $mock1 = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');
        $mock2 = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');

        $this->resolver->addResolvers([
            [$mock1, 1],
            [$mock2, 2],
        ]);

        $this->assertSame($mock2, $this->resolver->getResolvers()->top(), 'expecting Prioritized insertion');
    }

    public function testRemoveResolver()
    {
        $this->resolver->addResolver($survivor = $this->getMock('Zend\EventManager\Resolver\ResolverInterface'));
        $this->resolver->addResolver($delete = $this->getMock('Zend\EventManager\Resolver\ResolverInterface'));
        $this->resolver->removeResolver($delete);

        $resolvers = $this->resolver->getResolvers();
        $this->assertCount(1, $resolvers);
        $this->assertSame($survivor, $resolvers->top());
    }

    /**
     * @expectedException \Zend\EventManager\Exception\RuntimeException
     */
    public function testGetEventFailsIfNoResolvers()
    {
        $this->resolver->get('argg');
    }

    /**
     * @expectedException \Zend\EventManager\Exception\RuntimeException
     */
    public function testGetEventFailsIfResolversDoNotReturnAnEvent()
    {
        $resolver = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');
        $resolver
            ->expects($this->once())
            ->method('get')
            ->with($name = 'test')
            ->will($this->returnValue(new \stdClass()));

        $this->resolver->addResolver($resolver);
        $this->resolver->get($name);
    }

    public function testGetEventReturnFirstValidResolverResponse()
    {
        $resolverOk = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');
        $resolverOk
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expected = $this->getMock('Zend\EventManager\EventInterface')));

        $resolverFails = $this->getMock('Zend\EventManager\Resolver\ResolverInterface');
        $resolverFails
            ->expects($this->never())
            ->method('get');

        $this->resolver->addResolver($resolverOk, 1);
        $this->resolver->addResolver($resolverFails);

        $this->assertSame($expected, $this->resolver->get('test'));

    }
}