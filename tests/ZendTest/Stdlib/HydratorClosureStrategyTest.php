<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 */
class HydratorClosureStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The hydrator that is used during testing.
     *
     * @var HydratorInterface
     */
    private $hydrator;

    public function setUp()
    {
        $this->hydrator = new ObjectProperty();
    }
    
    public function testAddingStrategy()
    {
        $this->assertAttributeCount(0, 'strategies', $this->hydrator);

        $this->hydrator->addStrategy('myStrategy', new ClosureStrategy());

        $this->assertAttributeCount(1, 'strategies', $this->hydrator);
        //$this->assertAttributeCount(0, 'strategies', $this->hydrator);
    }
    
    public function testCheckStrategyEmpty()
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));
    }
    
    public function testCheckStrategyNotEmpty()
    {
        $this->hydrator->addStrategy('myStrategy', new ClosureStrategy());

        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testRemovingStrategy()
    {
        $this->assertAttributeCount(0, 'strategies', $this->hydrator);

        $this->hydrator->addStrategy('myStrategy', new ClosureStrategy());
        $this->assertAttributeCount(1, 'strategies', $this->hydrator);

        $this->hydrator->removeStrategy('myStrategy');
        $this->assertAttributeCount(0, 'strategies', $this->hydrator);
    }

    public function testRetrieveStrategy()
    {
        $strategy = new ClosureStrategy();
        $this->hydrator->addStrategy('myStrategy', $strategy);

        $this->assertEquals($strategy, $this->hydrator->getStrategy('myStrategy'));
    }

    public function testExtractingObjects()
    {
        $this->hydrator->addStrategy('entities', new ClosureStrategy());
        
        //\Zend\Debug\Debug::dump($this->hydrator->getStrategy('entities'));exit();
        
        $entityA = new TestAsset\HydratorClosureStrategy\Container();
        $entityA->addEntity(new TestAsset\HydratorClosureStrategy\SimpleEntity(111, 'AAA'));
        $entityA->addEntity(new TestAsset\HydratorClosureStrategy\SimpleEntity(222, 'BBB'));
        
        $attributes = $this->hydrator->extract($entityA);
        \Zend\Debug\Debug::dump($attributes);exit();
        
        $this->assertContains(111, $attributes['entities']);
        $this->assertContains(222, $attributes['entities']);
    }
    
//    public function testExtractingObjects()
//    {
//        $this->hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());
//
//        $entityA = new TestAsset\HydratorStrategyEntityA();
//        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(111, 'AAA'));
//        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(222, 'BBB'));
//
//        $attributes = $this->hydrator->extract($entityA);
//
//        $this->assertContains(111, $attributes['entities']);
//        $this->assertContains(222, $attributes['entities']);
//    }
//
//    public function testHydratingObjects()
//    {
//        $this->hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());
//
//        $entityA = new TestAsset\HydratorStrategyEntityA();
//        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(111, 'AAA'));
//        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(222, 'BBB'));
//
//        $attributes = $this->hydrator->extract($entityA);
//        $attributes['entities'][] = 333;
//
//        $this->hydrator->hydrate($attributes, $entityA);
//        $entities = $entityA->getEntities();
//
//        $this->assertCount(3, $entities);
//    }
//
//    /**
//     * @dataProvider underscoreHandlingDataProvider
//     */
//    public function testWhenUsingUnderscoreSeparatedKeysHydratorStrategyIsAlwaysConsideredUnderscoreSeparatedToo($underscoreSeparatedKeys, $formFieldKey)
//    {
//        $hydrator = new ClassMethods($underscoreSeparatedKeys);
//
//        $strategy = $this->getMock('Zend\Stdlib\Hydrator\Strategy\StrategyInterface');
//
//        $entity = new TestAsset\ClassMethodsUnderscore();
//        $value = $entity->getFooBar();
//
//        $hydrator->addStrategy($formFieldKey, $strategy);
//
//        $strategy
//            ->expects($this->once())
//            ->method('extract')
//            ->with($this->identicalTo($value))
//            ->will($this->returnValue($value))
//        ;
//
//        $attributes = $hydrator->extract($entity);
//
//        $strategy
//            ->expects($this->once())
//            ->method('hydrate')
//            ->with($this->identicalTo($value))
//            ->will($this->returnValue($value))
//        ;
//
//        $hydrator->hydrate($attributes, $entity);
//    }
//
//    public function underscoreHandlingDataProvider()
//    {
//        return array(
//            array(true, 'foo_bar'),
//            array(false, 'fooBar'),
//        );
//    }
}
