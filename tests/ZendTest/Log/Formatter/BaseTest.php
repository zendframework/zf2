<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use stdClass;
use EmptyIterator;
use ArrayIterator;
use ZendTest\Log\TestAsset\StringObject;
use Zend\Log\Formatter\Base as BaseFormatter;

/**
 * @group      Zend_Log
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultDateTimeFormat()
    {
        $formatter = new BaseFormatter();
        $this->assertEquals(BaseFormatter::DEFAULT_DATETIME_FORMAT, $formatter->getDateTimeFormat());
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testAllowsSpecifyingDateTimeFormatAsConstructorArgument($dateTimeFormat)
    {
        $formatter = new BaseFormatter($dateTimeFormat);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    /**
     * @return array
     */
    public function provideDateTimeFormats()
    {
        return [
            ['r'],
            ['U'],
            [DateTime::RSS],
        ];
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormat($dateTimeFormat)
    {
        $formatter = new BaseFormatter();
        $formatter->setDateTimeFormat($dateTimeFormat);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormatInConstructor($dateTimeFormat)
    {
        $options = ['dateTimeFormat' => $dateTimeFormat];
        $formatter = new BaseFormatter($options);

        $this->assertEquals($dateTimeFormat, $formatter->getDateTimeFormat());
    }

    public function testFormatAllTypes()
    {
        $datetime = new DateTime();
        $object = new stdClass();
        $object->foo = 'bar';
        $formatter = new BaseFormatter();

        $event = [
            'timestamp' => $datetime,
            'priority' => 1,
            'message' => 'tottakai',
            'extra' => [
                'float' => 0.2,
                'boolean' => false,
                'array_empty' => [],
                'array' => range(0, 4),
                'traversable_empty' => new EmptyIterator(),
                'traversable' => new ArrayIterator(['id', 42]),
                'null' => null,
                'object_empty' => new stdClass(),
                'object' => $object,
                'string object' => new StringObject(),
                'resource' => fopen('php://stdout', 'w'),
            ],
        ];
        $outputExpected = [
            'timestamp' => $datetime->format($formatter->getDateTimeFormat()),
            'priority' => 1,
            'message' => 'tottakai',
            'extra' => [
                'boolean' => false,
                'float' => 0.2,
                'array_empty' => '[]',
                'array' => '[0,1,2,3,4]',
                'traversable_empty' => '[]',
                'traversable' => '["id",42]',
                'null' => null,
                'object_empty' => 'object(stdClass) {}',
                'object' => 'object(stdClass) {"foo":"bar"}',
                'string object' => 'Hello World',
                'resource' => 'resource(stream)',
            ],
        ];

        $this->assertEquals($outputExpected, $formatter->format($event));
    }

    public function testFormatNoInfiniteLoopOnSelfReferencingArrayValues()
    {
        $datetime  = new DateTime();
        $formatter = new BaseFormatter();

        $selfRefArr = [];
        $selfRefArr['selfRefArr'] = & $selfRefArr;

        $event = [
            'timestamp' => $datetime,
            'priority'  => 1,
            'message'   => 'tottakai',
            'extra' => [
                'selfRefArr' => $selfRefArr,
            ],
        ];

        if (version_compare(PHP_VERSION, '5.5', 'lt')) {
            $outputExpected = [
                'timestamp' => $datetime->format($formatter->getDateTimeFormat()),
                'priority'  => 1,
                'message'   => 'tottakai',
                'extra' => [
                    'selfRefArr' => '{"selfRefArr":{"selfRefArr":null}}',
                ],
            ];
        } else {
            $outputExpected = [
                'timestamp' => $datetime->format($formatter->getDateTimeFormat()),
                'priority'  => 1,
                'message'   => 'tottakai',
                'extra' => [
                    'selfRefArr' => '',
                ],
            ];
        }

        $this->assertEquals($outputExpected, $formatter->format($event));
    }

    public function testFormatExtraArrayKeyWithNonArrayValue()
    {
        $formatter = new BaseFormatter();

        $event = [
            'message'   => 'Hi',
            'extra'     => '',
        ];
        $outputExpected = [
            'message' => 'Hi',
            'extra'   =>  '',
        ];

        $this->assertEquals($outputExpected, $formatter->format($event));
    }
}
