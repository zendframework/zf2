<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy;

/**
 * Tests for {@see \Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy}
 *
 * @covers \Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy
 */
class ExplodeStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getValidHydratedValues
     *
     * @param string   $expected
     * @param string   $delimiter
     * @param string[] $extractValue
     */
    public function testExtract($expected, $delimiter, $extractValue)
    {
        $strategy = new ExplodeStrategy($delimiter);

        if (is_numeric($expected)) {
            $this->assertEquals($expected, $strategy->extract($extractValue));
        } else {
            $this->assertSame($expected, $strategy->extract($extractValue));
        }
    }

    public function testGetExceptionWithInvalidArgumentOnExtraction()
    {
        $strategy = new ExplodeStrategy();

        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');

        $strategy->extract('');
    }

    public function testGetEmptyArrayWhenHydratingNullValue()
    {
        $strategy = new ExplodeStrategy();

        $this->assertSame([], $strategy->hydrate(null));
    }

    public function testGetExceptionWithEmptyDelimiter()
    {
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');

        new ExplodeStrategy('');
    }

    public function testGetExceptionWithInvalidDelimiter()
    {
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');

        new ExplodeStrategy([]);
    }

    public function testHydrateWithExplodeLimit()
    {
        $strategy = new ExplodeStrategy('-', 2);
        $this->assertSame(['foo', 'bar-baz-bat'], $strategy->hydrate('foo-bar-baz-bat'));

        $strategy = new ExplodeStrategy('-', '3');
        $this->assertSame(['foo', 'bar', 'baz-bat'], $strategy->hydrate('foo-bar-baz-bat'));
    }

    public function testHydrateWithInvalidScalarType()
    {
        $strategy = new ExplodeStrategy();

        $this->setExpectedException(
            'Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException',
            'Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy::hydrate expects argument 1 to be string,'
            . ' array provided instead'
        );

        $strategy->hydrate([]);
    }

    public function testHydrateWithInvalidObjectType()
    {
        $strategy = new ExplodeStrategy();

        $this->setExpectedException(
            'Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException',
            'Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy::hydrate expects argument 1 to be string,'
            . ' stdClass provided instead'
        );

        $strategy->hydrate(new \stdClass());
    }

    public function testExtractWithInvalidObjectType()
    {
        $strategy = new ExplodeStrategy();

        $this->setExpectedException(
            'Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException',
            'Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy::extract expects argument 1 to be array,'
            . ' stdClass provided instead'
        );

        $strategy->extract(new \stdClass());
    }

    /**
     * @dataProvider getValidHydratedValues
     *
     * @param mixed    $value
     * @param string   $delimiter
     * @param string[] $expected
     */
    public function testHydration($value, $delimiter, array $expected)
    {
        $strategy = new ExplodeStrategy($delimiter);

        $this->assertSame($expected, $strategy->hydrate($value));
    }

    /**
     * Data provider
     *
     * @return mixed[][]
     */
    public function getValidHydratedValues()
    {
        return [
            [null, ',', []],
            ['', ',', ['']],
            ['foo', ',', ['foo']],
            ['foo,bar', ',', ['foo', 'bar']],
            ['foo.bar', '.', ['foo', 'bar']],
            ['foo.bar', ',', ['foo.bar']],
            [123, ',', ['123']],
            [123, '2', ['1', '3']],
            [123.456, ',', ['123.456']],
            [123.456, '.', ['123', '456']],
            ['foo,bar,dev,null', ',', ['foo', 'bar', 'dev', 'null']],
            ['foo;bar;dev;null', ';', ['foo', 'bar', 'dev', 'null']],
            ['', ',', ['']],
        ];
    }
}
