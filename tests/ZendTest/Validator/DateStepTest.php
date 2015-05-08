<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator;
use DateTime;
use DateInterval;
use DateTimeZone;

/**
 * @group      Zend_Validator
 */
class DateStepTest extends \PHPUnit_Framework_TestCase
{
    public function stepTestsDataProvider()
    {
        $data = [
            //    interval format            baseValue               value                  isValid
            ['PT1S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:00Z', true ],
            ['PT1S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-03T00:00:00Z', true ],
            ['PT1S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:02Z', true ],
            ['PT2S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:01Z', false],
            ['PT2S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:00:16Z', true ],
            ['PT2S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-03T00:00:00Z', true ],
            // minutes
            ['PT1M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ],
            ['PT1M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:30Z', false],
            ['PT1M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:02:00Z', true ],
            ['PT2M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:01:00Z', false],
            ['PT2M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T00:16:00Z', true ],
            ['PT2M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ],
            ['PT1M', 'H:i:s',           '00:00:00',             '12:34:00',             true ],
            ['PT2M', 'H:i:s',           '00:00:00',             '12:34:00',             true ],
            ['PT2M', 'H:i:s',           '00:00:00',             '12:35:00',             false],
            // hours
            ['PT1H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ],
            ['PT1H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:30Z', false],
            ['PT1H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T02:00:00Z', true ],
            ['PT2H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T01:00:00Z', false],
            ['PT2H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-01T16:00:00Z', true ],
            ['PT2H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-01T00:00:00Z', true ],
            // days
            ['P1D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:00Z', true ],
            ['P1D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:30Z', false],

            ['P1D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '2014-08-12T00:00:00Z', true],

            ['P2D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-02T00:00:00Z', false],
            ['P2D',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-15T00:00:00Z', true ],
            ['P2D',  DateTime::ISO8601, '1971-01-01T00:00:00Z', '1973-01-01T00:00:00Z', false],
            ['P2D',  DateTime::ISO8601, '2000-01-01T00:00:00Z', '2001-01-01T00:00:00Z', true ], // leap year
            // weeks
            ['P1W',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-01-29T00:00:00Z', true ],
            // months
            ['P1M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:00Z', true ],
            ['P1M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:30Z', false],
            ['P2M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-02-01T00:00:00Z', false],
            ['P2M',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1971-05-01T00:00:00Z', true ],
            ['P1M',  'Y-m',             '1970-01',              '1970-10',              true ],
            ['P2M',  '!Y-m',            '1970-01',              '1970-11',              true ],
            ['P2M',  'Y-m',             '1970-01',              '1970-10',              false],
            // years
            ['P1Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:00Z', true ],
            ['P1Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1973-01-01T00:00:30Z', false],
            ['P2Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1971-01-01T00:00:00Z', false],
            ['P2Y',  DateTime::ISO8601, '1970-01-01T00:00:00Z', '1976-01-01T00:00:00Z', true ],
            // complex
            ['P2M2DT12H', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-03T12:00:00Z', true ],
            ['P2M2DT12M', DateTime::ISO8601, '1970-01-01T00:00:00Z', '1970-03-03T12:00:00Z', false],
            // long interval
            ['PT1M20S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '2020-09-13T12:26:40Z', true], // 20,000,000 steps
            ['PT1M20S', DateTime::ISO8601, '1970-01-01T00:00:00Z', '2020-09-13T12:26:41Z', false],

            ['P2W',  'Y-\WW',           '1970-W01',             '1973-W16',             true ],
            ['P2W',  'Y-\WW',           '1970-W01',             '1973-W17',             false],
        ];

        return $data;
    }

    /**
     * @dataProvider stepTestsDataProvider
     */
    public function testDateStepValidation($interval, $format, $baseValue, $value, $isValid)
    {
        $validator = new Validator\DateStep([
            'format'       => $format,
            'baseValue'    => $baseValue,
            'step' => new DateInterval($interval),
        ]);

        $this->assertEquals($isValid, $validator->isValid($value));
    }

    public function testGetMessagesReturnsDefaultValue()
    {
        $validator = new Validator\DateStep();
        $this->assertEquals([], $validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator  = new Validator\DateStep([]);
        $this->assertObjectHasAttribute('messageTemplates', $validator);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }

    public function testStepError()
    {
        $validator = new Validator\DateStep([
            'format'       => 'Y-m-d',
            'baseValue'    => '2012-01-23',
            'step' => new DateInterval("P10D"),
        ]);

        $this->assertFalse($validator->isValid('2012-02-23'));
    }

    public function moscowWinterTimeDataProvider()
    {
        // dates before during and after Moscow's wintertime
        return [
            ['26-03-1999'],
            ['26-03-2011'],
            ['27-03-2011'],
            ['26-03-2015'],
        ];
    }

    /**
     * @dataProvider moscowWinterTimeDataProvider
     */
    public function testMoscowWinterTime($dateToValidate)
    {
        $validator = new Validator\DateStep([
            'format' => 'd-m-Y',
            'baseValue' => date('d-m-Y', 0),
            'step' => new DateInterval("P1D"),
            'timezone' => new DateTimeZone('Europe/Moscow'),
        ]);

        $this->assertTrue($validator->isValid($dateToValidate));
    }
}
