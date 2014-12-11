<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ZendTest\Stdlib\Hydrator;

/**
 * SimpleArraySerializableTest
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class SimpleArraySerializableTest extends \PHPUnit_Framework_TestCase
{
    protected $hydrator;


    protected function setUp()
    {
        $this->hydrator = new \Zend\Stdlib\Hydrator\SimpleArraySerializable();
    }

    /**
     *
     * @dataProvider provider
     */
    public function testExtract($object, $value)
    {
        $object->expects($this->once())
            ->method('getArrayCopy')
            ->will($this->returnValue($value));

        $this->assertEquals($value, $this->hydrator->extract($object));
    }

    /**
     *
     * @dataProvider provider
     */
    public function testHydrate($object, $value)
    {
        $object->expects($this->once())
            ->method('exchangeArray')
            ->with($value)
            ->will($this->returnSelf());

        $this->assertEquals($object, $this->hydrator->hydrate($value, $object));
    }

    public function provider()
    {
        return array(
            array(
                $this->getMock('Zend\Stdlib\ArraySerializableInterface', array('getArrayCopy', 'exchangeArray')),
                array(
                    'foo' => 1,
                    'bar' => 'test',
                ),
            ),
        );
    }
}
