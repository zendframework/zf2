<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Password;

use Zend\Crypt\Password\HandlerAggregate;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Zend\Crypt\Password\HandlerAggregate
 */
class HandlerAggregateTest extends TestCase
{
    public function testSupports()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setHashingMethods(array());

        $this->assertFalse(
            $aggregate->supports('foobar'),
            'Unknown hash must return false'
        );

        $handler = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handler->expects($this->once())
                ->method('supports')
                ->with($this->equalTo('bazbat'))
                ->will($this->returnValue(true));

        $aggregate->getHandlerManager()->setService('foobar', $handler);
        $aggregate->getOptions()->setHashingMethods(array('foobar'));

        $this->assertTrue(
            $aggregate->supports('bazbat'),
            'Known hash must return true'
        );
    }

    public function testHash()
    {
        $handler = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handler->expects($this->once())
                ->method('hash')
                ->with($this->equalTo('foobar'))
                ->will($this->returnValue('bazbat'));

        $aggregate = new HandlerAggregate();
        $aggregate->getHandlerManager()->setService('foobar', $handler);
        $aggregate->getOptions()->setDefaultHashingMethod('foobar');

        $this->assertEquals('bazbat', $aggregate->hash('foobar'));
    }

    public function testCompare()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setHashingMethods(array());

        $this->assertFalse(
            $aggregate->compare('foobar', 'bazbat'),
            'Unknown hash must be considered not matching'
        );

        $handler = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handler->expects($this->once())
                ->method('supports')
                ->with($this->equalTo('bazbat'))
                ->will($this->returnValue(true));
        $handler->expects($this->once())
                ->method('compare')
                ->with($this->equalTo('foobar'), $this->equalTo('bazbat'))
                ->will($this->returnValue(true));

        $aggregate->getHandlerManager()->setService('foobar', $handler);
        $aggregate->getOptions()->setHashingMethods(array('foobar'));

        $this->assertTrue(
            $aggregate->compare('foobar', 'bazbat'),
            'Known hash matched by handler must be considered matching'
        );
    }

    public function testShouldRehashWithUnknownHash()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setDefaultHashingMethod('foobar')
                                ->setHashingMethods(array());

        $this->assertTrue(
            $aggregate->shouldRehash('bazbat'),
            'Unknwon hash must trigger rehash'
        );
    }

    public function testShouldRehashWithHandlerReportingRehash()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setDefaultHashingMethod('foobar')
                                ->setHashingMethods(array('foobar'));

        $handler = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handler->expects($this->once())
                ->method('supports')
                ->with($this->equalTo('bazbat'))
                ->will($this->returnValue(true));
        $handler->expects($this->once())
                ->method('shouldRehash')
                ->with($this->equalTo('bazbat'))
                ->will($this->returnValue(true));

        $aggregate->getHandlerManager()->setService('foobar', $handler);

        $this->assertTrue(
            $aggregate->shouldRehash('bazbat'),
            'Known hash reported to rehash by handler must trigger rehash'
        );
    }

    public function testShouldRehashWithHandlerReportingNoRehash()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setDefaultHashingMethod('foobar')
                                ->setHashingMethods(array('foobar'));

        $handler = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handler->expects($this->once())
                ->method('supports')
                ->with($this->equalTo('bazbat'))
                ->will($this->returnValue(true));
        $handler->expects($this->once())
                ->method('shouldRehash')
                ->with($this->equalTo('bazbat'))
                ->will($this->returnValue(false));

        $aggregate->getHandlerManager()->setService('foobar', $handler);

        $this->assertFalse(
            $aggregate->shouldRehash('bazbat'),
            'Known hash reported to not rehash must not trigger rehash'
        );
    }

    public function testShouldRehashWithObsoleteHash()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setDefaultHashingMethod('foobar')
                                ->setHashingMethods(array('foobar', 'bazbat'));

        $handlerA = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handlerA->expects($this->once())
                 ->method('supports')
                 ->with($this->equalTo('bazbat'))
                 ->will($this->returnValue(false));
        $handlerB = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handlerB->expects($this->once())
                 ->method('supports')
                 ->with($this->equalTo('bazbat'))
                 ->will($this->returnValue(true));
        $handlerB->expects($this->once())
                 ->method('shouldRehash')
                 ->with($this->equalTo('bazbat'))
                 ->will($this->returnValue(false));

        $aggregate->getHandlerManager()->setService('foobar', $handlerA);
        $aggregate->getHandlerManager()->setService('bazbat', $handlerB);

        $this->assertTrue(
            $aggregate->shouldRehash('bazbat'),
            'Known hash not matching default hashing method must trigger rehash'
        );
    }

    public function testShouldRehashWithObsoleteHashAndMigrationDisabled()
    {
        $aggregate = new HandlerAggregate();
        $aggregate->getOptions()->setDefaultHashingMethod('foobar')
                                ->setHashingMethods(array('foobar', 'bazbat'))
                                ->setMigrateToDefaultHashingMethod(false);

        $handlerA = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handlerA->expects($this->once())
                 ->method('supports')
                 ->with($this->equalTo('bazbat'))
                 ->will($this->returnValue(false));
        $handlerB = $this->getMock('Zend\Crypt\Password\HandlerInterface');
        $handlerB->expects($this->once())
                 ->method('supports')
                 ->with($this->equalTo('bazbat'))
                 ->will($this->returnValue(true));
        $handlerB->expects($this->once())
                 ->method('shouldRehash')
                 ->with($this->equalTo('bazbat'))
                 ->will($this->returnValue(false));

        $aggregate->getHandlerManager()->setService('foobar', $handlerA);
        $aggregate->getHandlerManager()->setService('bazbat', $handlerB);

        $this->assertFalse(
            $aggregate->shouldRehash('bazbat'),
            'Known hash not matching default hashing method with migration disabled must not trigger rehash'
        );
    }
}
