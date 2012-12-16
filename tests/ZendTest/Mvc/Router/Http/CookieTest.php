<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request as Request;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Uri\Http as HttpUri;
use Zend\Mvc\Router\Http\Cookie;
use ZendTest\Mvc\Router\FactoryTester;

class CookieTest
    extends TestCase
{

    public function testFactory()
    {
        $tester = new FactoryTester( $this );
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Cookie',
            array(
            'cookies' => 'Missing "cookies" in options array'
            ), array(
            'cookies' => 'thing'
            )
        );
    }

    public static function routeProvider()
    {
        return array(
        );
    }

}
