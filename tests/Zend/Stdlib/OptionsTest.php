<?php

namespace ZendTest\Stdlib;

use ArrayObject;
use ZendTest\Stdlib\TestAsset\TestOptions;
use Zend\Stdlib\Exception\InvalidArgumentException;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    protected $config = array(
        'foo'         => 1,
        'foo_bar'     => 2,
        'foo_bar_baz' => 3,
    );

    public function testConstructionWithArray()
    {
        $options = new TestOptions($this->config);
        
        $this->assertEquals(1, $options->getFoo());
    }

    public function testConstructionWithTraversable()
    {
        $config = new ArrayObject(array('foo_bar' => 1));
        $options = new TestOptions($config);

        $this->assertEquals(1, $options->foo_bar);
    }

    public function testConstructionWithNull()
    {
        try {
            $options = new TestOptions(null);
        } catch(InvalidArgumentException $e) {
            $this->fail("Unexpected InvalidArgumentException raised");
        }
    }

    public function testMagickSetter()
    {
        $options = new TestOptions($this->config);
        $options->foo_bar = 10;

        $this->assertEquals(10, $options->getFooBar());
    }

    public function testMagickAccessor()
    {
        $options = new TestOptions($this->config);

        $this->assertEquals(3, $options->foo_bar_baz);
    }


    public function testUnsetting()
    {
        $options = new TestOptions($this->config);
        
        $this->assertEquals(true, isset($options->foo_bar));
        unset($options->foo_bar);
        $this->assertEquals(false, isset($options->foo_bar));
        
    }

    public function testSettingOptionRaisesException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\BadMethodCallException',
                                    "does not have a matching 'setUnknown' setter method");
        $options = new TestOptions(array(
            'unknown' => false
        ));
    }

    public function testGettingOptionRaisesException()
    {
        $options = new TestOptions();
        $this->setExpectedException('Zend\Stdlib\Exception\BadMethodCallException',
                                    "does not have a matching 'getUnknown' getter method");

        $unknown = $options->unknown;
    }


    public function testSettingFromArray()
    {
        $options = new TestOptions();
        $options->fromArray($this->config);

        $this->assertEquals(1, $options->getFoo());
    }

}
