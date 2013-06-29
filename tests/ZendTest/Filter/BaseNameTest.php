<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\BaseName as BaseNameFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class BaseNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = new BaseNameFilter();
        $valuesExpected = array(
            '/path/to/filename'     => 'filename',
            '/path/to/filename.ext' => 'filename.ext'
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input));
        }
    }

    public function dataNotStringValues()
    {
        return array(
            'int' => array(
                'value' => 1
            ),
            'null' => array(
                'value' => null
            ),
            'object' => array(
                'value' => new \ArrayObject()
            ),
            'array' => array(
                'value' => array()
            ),
            'closure' => array(
                'value' => function(){}
            )
        );
    }

    /**
     * @dataProvider dataNotStringValues
     */
    public function testFilterNotString($value)
    {
        $filter = new BaseNameFilter();

        $this->assertSame($value, $filter->filter($value));
    }
}
