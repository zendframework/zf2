<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Boolean as BooleanFilter;

/**
 * @group      Zend_Filter
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new BooleanFilter([
            'type'    => BooleanFilter::TYPE_INTEGER,
            'casting' => false,
        ]);

        $this->assertEquals(BooleanFilter::TYPE_INTEGER, $filter->getType());
        $this->assertFalse($filter->getCasting());
    }

    public function testConstructorParams()
    {
        $filter = new BooleanFilter(BooleanFilter::TYPE_INTEGER, false);

        $this->assertEquals(BooleanFilter::TYPE_INTEGER, $filter->getType());
        $this->assertFalse($filter->getCasting());
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new BooleanFilter();
        $this->assertSame($expected, $filter->filter($value));
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider noCastingTestProvider
     */
    public function testNoCasting($value, $expected)
    {
        $filter = new BooleanFilter('all', false);
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param int $type
     * @param array $testData
     * @dataProvider typeTestProvider
     */
    public function testTypes($type, $testData)
    {
        $filter = new BooleanFilter($type);
        foreach ($testData as $data) {
            list($value, $expected) = $data;
            $message = sprintf(
                '%s (%s) is not filtered as %s; type = %s',
                var_export($value, true),
                gettype($value),
                var_export($expected, true),
                $type
            );
            $this->assertSame($expected, $filter->filter($value), $message);
        }
    }

    /**
     * @param array $typeData
     * @param array $testData
     * @dataProvider combinedTypeTestProvider
     */
    public function testCombinedTypes($typeData, $testData)
    {
        foreach ($typeData as $type) {
            $filter = new BooleanFilter(['type' => $type]);
            foreach ($testData as $data) {
                list($value, $expected) = $data;
                $message = sprintf(
                    '%s (%s) is not filtered as %s; type = %s',
                    var_export($value, true),
                    gettype($value),
                    var_export($expected, true),
                    var_export($type, true)
                );
                $this->assertSame($expected, $filter->filter($value), $message);
            }
        }
    }

    public function testLocalized()
    {
        $filter = new BooleanFilter([
            'type' => BooleanFilter::TYPE_LOCALIZED,
            'translations' => [
                'yes' => true,
                'y'   => true,
                'no'  => false,
                'n'   => false,
                'yay' => true,
                'nay' => false,
            ]
        ]);

        $this->assertTrue($filter->filter('yes'));
        $this->assertTrue($filter->filter('yay'));
        $this->assertFalse($filter->filter('n'));
        $this->assertFalse($filter->filter('nay'));
    }

    public function testSettingFalseType()
    {
        $filter = new BooleanFilter();
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown type value');
        $filter->setType(true);
    }

    public function testGettingDefaultType()
    {
        $filter = new BooleanFilter();
        $this->assertEquals(127, $filter->getType());
    }

    public static function defaultTestProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, false],
            [1, true],
            [0.0, false],
            [1.0, true],
            ['', false],
            ['abc', true],
            ['0', false],
            ['1', true],
            [[], false],
            [[0], true],
            [null, false],
            ['false', true],
            ['true', true],
            ['no', true],
            ['yes', true],
        ];
    }

    public static function noCastingTestProvider()
    {
        return [
            [false, false],
            [true, true],
            [0, false],
            [1, true],
            [2, 2],
            [0.0, false],
            [1.0, true],
            [0.5, 0.5],
            ['', false],
            ['abc', 'abc'],
            ['0', false],
            ['1', true],
            ['2', '2'],
            [[], false],
            [[0], [0]],
            [null, null],
            ['false', false],
            ['true', true],
        ];
    }

    public static function typeTestProvider()
    {
        return [
            [
                BooleanFilter::TYPE_BOOLEAN,
                [
                    [false, false],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_INTEGER,
                [
                    [false, true],
                    [true, true],
                    [0, false],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_FLOAT,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, false],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_STRING,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', false],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_ZERO_STRING,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', false],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_EMPTY_ARRAY,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], false],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_NULL,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, false],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_PHP,
                [
                    [false, false],
                    [true, true],
                    [0, false],
                    [1, true],
                    [0.0, false],
                    [1.0, true],
                    ['', false],
                    ['abc', true],
                    ['0', false],
                    ['1', true],
                    [[], false],
                    [[0], true],
                    [null, false],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_FALSE_STRING,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', false],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            // default behaviour with no translations provided
            // all values filtered as true
            [
                BooleanFilter::TYPE_LOCALIZED,
                [
                    [false, true],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', true],
                    ['abc', true],
                    ['0', true],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
            [
                BooleanFilter::TYPE_ALL,
                [
                    [false, false],
                    [true, true],
                    [0, false],
                    [1, true],
                    [0.0, false],
                    [1.0, true],
                    ['', false],
                    ['abc', true],
                    ['0', false],
                    ['1', true],
                    [[], false],
                    [[0], true],
                    [null, false],
                    ['false', false],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ],
        ];
    }

    public static function combinedTypeTestProvider()
    {
        return [
            [
                [
                    [
                        BooleanFilter::TYPE_ZERO_STRING,
                        BooleanFilter::TYPE_STRING,
                        BooleanFilter::TYPE_BOOLEAN,
                    ],
                    [
                        'zero',
                        'string',
                        'boolean',
                    ],
                    BooleanFilter::TYPE_ZERO_STRING | BooleanFilter::TYPE_STRING | BooleanFilter::TYPE_BOOLEAN,
                    BooleanFilter::TYPE_ZERO_STRING + BooleanFilter::TYPE_STRING + BooleanFilter::TYPE_BOOLEAN,
                ],
                [
                    [false, false],
                    [true, true],
                    [0, true],
                    [1, true],
                    [0.0, true],
                    [1.0, true],
                    ['', false],
                    ['abc', true],
                    ['0', false],
                    ['1', true],
                    [[], true],
                    [[0], true],
                    [null, true],
                    ['false', true],
                    ['true', true],
                    ['no', true],
                    ['yes', true],
                ]
            ]
        ];
    }
}
