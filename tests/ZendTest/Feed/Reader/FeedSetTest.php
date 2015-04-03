<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Feed\Reader;

use PHPUnit_Framework_TestCase;
use ReflectionMethod;
use Zend\Feed\Reader\FeedSet;

class FeedSetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FeedSet
     */
    protected $feedSet;

    protected function setUp()
    {
        $this->feedSet = new FeedSet();
    }

    /**
     * @dataProvider linkAndUriProvider
     */
    public function testAbsolutiseUri($link, $uri)
    {
        $method = new ReflectionMethod('Zend\Feed\Reader\FeedSet', 'absolutiseUri');
        $method->setAccessible(true);

        $this->assertEquals('http://example.com/feed', $method->invokeArgs($this->feedSet, array($link, $uri)));
    }

    public function linkAndUriProvider()
    {
        return array(
            array('feed', 'http://example.com'),
            array('feed', '//example.com'),
            array('//feed','//example.com'),
        );
    }
}
