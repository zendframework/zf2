<?php
namespace ZendTest\Filter;

use Zend\Filter\StripTabs;

/**
 * @group Zend_Filter
 */
class StripTabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StripTabs
     */
    protected $instance;

    /**
     * @return array
     */
    public function filterDataProvider()
    {
        return array(
            array('', ''),
            array(null, null),
            array('\t', '\t'),
            array(array('\t'), array('\t')),

            array("\t", ''),
            array("\t\t", ''),
            array(array("\t\t"), array('')),
            array("A tab in \ttext", 'A tab in text'),
        );
    }

    public function setup()
    {
        $this->instance = new StripTabs();
    }

    /**
     * @dataProvider filterDataProvider
     * @param mixed $value
     * @param mixed $exValue
     */
    public function testFilter($value, $exValue)
    {
        $actual = $this->instance->filter($value);
        $this->assertSame($exValue, $actual);
    }
}
