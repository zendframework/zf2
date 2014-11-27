<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
namespace ZendTest\Mvc\View\Console;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;
use Zend\Console\Adapter\Posix;
use Zend\Console\ColorInterface;
use Zend\Mvc\View\Console\RouteNotFoundStrategy;

class RouteNotFoundStrategyTest extends TestCase
{
    /**
     * @var RouteNotFoundStrategy
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new RouteNotFoundStrategy();
    }

    public function testRenderTableConcatenateAndInvalidInputDoesNotThrowException()
    {
        $reflection = new ReflectionClass('Zend\Mvc\View\Console\RouteNotFoundStrategy');
        $method = $reflection->getMethod('renderTable');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->strategy, array(array(array()), 1, 0));
        $this->assertSame('', $result);
    }

	/**
     * Test that the renderTable() method will display a nicely formatted table
     * when using the posix console adapter and colorized strings.
     * 
     * @group issue-6922
     */
    public function testRenderTableCalculateCorrectStringLengthForPosixColorizedString() {
        $reflection = new ReflectionClass('Zend\Mvc\View\Console\RouteNotFoundStrategy');
        $method = $reflection->getMethod('renderTable');
        $method->setAccessible(true);

        // Colorized route
        $console = new Posix();
        $route = $console->colorize('some route', ColorInterface::GREEN);

        $table[] = array($route, 'A long explanation that will trigger a line break.');

        // 2 table columns in a 60 column terminal
        $result = $method->invokeArgs($this->strategy, array($table, 2, 60));
        $expected = "  $route    A long explanation that will trigger a     \n" .
                "                line break.                                \n";

        $this->assertSame($expected, $result);
    }

}
