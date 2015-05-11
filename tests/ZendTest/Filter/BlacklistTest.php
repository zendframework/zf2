<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\FilterPluginManager;
use Zend\Filter\Blacklist as BlacklistFilter;
use Zend\Stdlib\ArrayObject;

/**
 * @group      Zend_Filter
 */
class BlacklistTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new BlacklistFilter([
            'list'    => ['test', 1],
            'strict'  => true,
        ]);

        $this->assertEquals(true, $filter->getStrict());
        $this->assertEquals(['test', 1], $filter->getList());
    }

    public function testConstructorDefaults()
    {
        $filter = new BlacklistFilter();

        $this->assertEquals(false, $filter->getStrict());
        $this->assertEquals([], $filter->getList());
    }

    public function testWithPluginManager()
    {
        $pluginManager = new FilterPluginManager();
        $filter = $pluginManager->get('blacklist');

        $this->assertInstanceOf('Zend\Filter\Blacklist', $filter);
    }

    public function testNullListShouldThrowException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');
        $filter = new BlacklistFilter([
            'list' => null,
        ]);
    }

    public function testTraversableConvertsToArray()
    {
        $array = ['test', 1];
        $obj = new ArrayObject(['test', 1]);
        $filter = new BlacklistFilter([
            'list' => $obj,
        ]);
        $this->assertEquals($array, $filter->getList());
    }

    public function testSetStrictShouldCastToBoolean()
    {
        $filter = new BlacklistFilter([
            'strict' => 1
        ]);
        $this->assertSame(true, $filter->getStrict());
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new BlacklistFilter();
        $this->assertSame($expected, $filter->filter($value));
    }

    /**
     * @param bool $strict
     * @param array $testData
     * @dataProvider listTestProvider
     */
    public function testList($strict, $list, $testData)
    {
        $filter = new BlacklistFilter([
            'strict' => $strict,
            'list'   => $list,
        ]);
        foreach ($testData as $data) {
            list($value, $expected) = $data;
            $message = sprintf(
                '%s (%s) is not filtered as %s; type = %s',
                var_export($value, true),
                gettype($value),
                var_export($expected, true),
                $strict
            );
            $this->assertSame($expected, $filter->filter($value), $message);
        }
    }

    public static function defaultTestProvider()
    {
        return [
            ['test',   'test'],
            [0,        0],
            [0.1,      0.1],
            [[],  []],
            [null,     null],
        ];
    }

    public static function listTestProvider()
    {
        return [
            [
                true, //strict
                ['test', 0],
                [
                    ['test',   null],
                    [0,        null],
                    [null,     null],
                    [false,    false],
                    [0.0,      0.0],
                    [[],  []],
                ],
            ],
            [
                false, //not strict
                ['test', 0],
                [
                    ['test',   null],
                    [0,        null],
                    [null,     null],
                    [false,    null],
                    [0.0,      null],
                    [0.1,      0.1],
                    [[],  []],
                ],
            ],
        ];
    }
}
