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

use Zend\Filter\FilterChain;
use Zend\Filter\AbstractFilter;
use Zend\Filter\FilterPluginManager;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @cover      Zend\Filter\FilterChain
 */
class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterChain
     */
    protected $filterChain;

    public function setUp()
    {
        $this->filterChain = new FilterChain(new FilterPluginManager());
    }

    public function testAssertDefaultPriority()
    {
        $this->assertEquals(1, FilterChain::DEFAULT_PRIORITY);
    }

    public function testCountableInterface()
    {
        $this->assertEquals(0, count($this->filterChain));

        $this->filterChain->attach(new StripUpperCase());
        $this->assertEquals(1, count($this->filterChain));
    }

    public function testCanAttachAnyCallableOrFilter()
    {
        $this->filterChain->attach(function($value) {return $value; });
        $this->filterChain->attach(new StripUpperCase());

        $this->assertEquals(2, count($this->filterChain));
    }

    public function testThrowExceptionIfInvalidFilter()
    {
        $this->setExpectedException('Exception');
        $this->filterChain->attach(new \stdClass());
    }

    public function testCanAttachByName()
    {
        $callback = function($value) {return $value;};

        $this->filterChain->attachByName('Callback', array('callback' => $callback));

        $filter = $this->filterChain->getFilters()->top();

        $this->assertInstanceOf('Zend\Filter\Callback', $filter);
        $this->assertSame($callback, $filter->getCallback());
    }

    public function testCanMergeTwoFilterChains()
    {
        $this->filterChain->attach(new LowerCase());

        $anotherFilterChain = new FilterChain(new FilterPluginManager());
        $anotherFilterChain->attach(new StripUpperCase());

        $this->filterChain->merge($anotherFilterChain);

        $this->assertEquals(2, count($this->filterChain));
    }

    public function testCanSetFiltersUsingInstances()
    {
        $filter1 = new LowerCase();
        $filter2 = function($value) {return $value;};

        $this->filterChain->setFilters(array($filter1, $filter2));

        $this->assertEquals(2, count($this->filterChain));
        $this->assertSame($filter1, $this->filterChain->getFilters()->extract());
        $this->assertSame($filter2, $this->filterChain->getFilters()->extract());
    }

    public function testCanSetFiltersUsingSpecification()
    {
        $specification = array(
            array('name' => 'Callback', 'priority' => 10),
            array('name' => 'Boolean', 'priority' => -10)
        );

        $this->filterChain->setFilters($specification);

        $this->assertEquals(2, count($this->filterChain));

        $filters = $this->filterChain->getFilters();
        $this->assertInstanceOf('Zend\Filter\Callback', $filters->extract());
        $this->assertInstanceOf('Zend\Filter\Boolean', $filters->extract());
    }

    public function testRespectPriorities()
    {
        $filter1 = function($value) { return str_replace('_', ' ', $value); };
        $filter2 = function($value) { return ucwords($value); };

        $this->filterChain->attach($filter1, 10);
        $this->filterChain->attach($filter2, 0);

        $this->assertEquals('Php Is Good', $this->filterChain->filter('php_is_good'));
    }
}


class LowerCase extends AbstractFilter
{
    public function filter($value)
    {
        return strtolower($value);
    }
}


class StripUpperCase extends AbstractFilter
{
    public function filter($value)
    {
        return preg_replace('/[A-Z]/', '', $value);
    }
}
