<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;

class ClosureStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function extractDataProvider()
    {
        return [
            [
                function ($value) { return strtoupper($value); },
                new \ArrayObject(['foo' => 'foo', 'bar' => 'bar']),
                ['foo' => 'FOO', 'bar' => 'BAR'],
            ],
            [
                function ($value, $data) { return isset($data['bar']) ? strtoupper($value) : $value; },
                new \ArrayObject(['foo' => 'foo', 'bar' => 'bar']),
                ['foo' => 'FOO', 'bar' => 'BAR'],
            ],
            [
                function ($value, $data) { return isset($data['bar']) ? strtoupper($value) : $value; },
                new \ArrayObject(['foo' => 'foo', 'baz' => 'baz']),
                ['foo' => 'foo', 'baz' => 'baz'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function hydrateDataProvider()
    {
        return [
            [
                function ($value) { return strtoupper($value); },
                ['foo' => 'foo', 'bar' => 'bar'],
                ['foo' => 'FOO', 'bar' => 'BAR'],
            ],
            [
                function ($value, $data) { return strtoupper($value); },
                ['foo' => 'foo', 'bar' => 'bar'],
                ['foo' => 'FOO', 'bar' => 'BAR'],
            ],
            [
                function ($value, $data) { return isset($data['bar']) ? strtoupper($value) : $value; },
                ['foo' => 'foo', 'bar' => 'bar'],
                ['foo' => 'FOO', 'bar' => 'BAR'],
            ],
            [
                function ($value, $data) { return isset($data['bar']) ? strtoupper($value) : $value; },
                ['foo' => 'foo', 'baz' => 'baz'],
                ['foo' => 'foo', 'baz' => 'baz'],
            ],
        ];
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Strategy\ClosureStrategy::extract()
     * @dataProvider extractDataProvider
     *
     * @param Callable $extractFunc
     * @param array    $data
     * @param array    $expected
     */
    public function testExtract($extractFunc, $data, $expected)
    {
        $strategy = new ClosureStrategy($extractFunc);

        $actual = [];
        foreach ($data as $k => $value) {
            $actual[$k] = $strategy->extract($value, $data);
        }

        $this->assertSame($actual, $expected);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Strategy\ClosureStrategy::hydrate()
     * @dataProvider hydrateDataProvider
     *
     * @param Callable $hydrateFunc
     * @param array    $data
     * @param array    $expected
     */
    public function testHydrate($hydrateFunc, $data, $expected)
    {
        $strategy = new ClosureStrategy(null, $hydrateFunc);

        $actual = [];
        foreach ($data as $k => $value) {
            $actual[$k] = $strategy->hydrate($value, $data);
        }

        $this->assertSame($actual, $expected);
    }
}
