<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Plugin;
use Zend\Controller;


/**
 * Test class for Zend_Controller_Plugin_ErrorHandler.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Plugin
 */
class HttpGatewayCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The plugin under test
     * 
     * @var Zend\Controller\Plugin\HttpGatewayCache
     */
    protected $_plugin;
    
    public function setUp()
    {
        Controller\Front::getInstance()->resetInstance();
        Controller\Front::getInstance()->setControllerDirectory(dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files')
                                       ->setParam('noErrorHandler', true)
                                       ->setParam('noViewRenderer', true)
                                       ->returnResponse(true)
                                       ->throwExceptions(true);
    }
    
    protected function tearDown()
    {
        $this->_plugin = null;
    }
    
    public function testServesRequestsFromTheCache()
    {
        $cacheStub = $this->getMock('\Zend\Cache\Frontend\Core', array('load'));
        $cacheStub->expects($this->once())
                  ->method('load')
                  ->with($this->equalTo(md5('/test')))
                  ->will($this->returnValue('#test#'));
        
        $request = new Controller\Request\Http('http://example.com/test');
        $response = new Controller\Response\Cli();
        
        $this->_plugin = new Controller\Plugin\HttpGatewayCache($cacheStub);
        $this->_plugin->setResponse($response)
                      ->dispatchLoopStartup($request);
        
        $this->assertEquals('#test#', $this->_plugin->getResponse()->getBody());
    }
    
    public function testServesRequestsFromTheCacheAndProcessEsiTags()
    {
        $cacheStub = $this->getMock('\Zend\Cache\Frontend\Core', array('load'));
        $cacheStub->expects($this->once())
                  ->method('load')
                  ->with($this->equalTo(md5('/test')))
                  ->will($this->returnValue('<esi:include src="http://example.com/esi" onerror="continue" />'));
        
        $request = new Controller\Request\Http('http://example.com/test');
        $response = new Controller\Response\Cli();
        
        $this->_plugin = new Controller\Plugin\HttpGatewayCache($cacheStub);
        $this->_plugin->setResponse($response)
                      ->dispatchLoopStartup($request);
        
        $this->assertEquals("processed esi content\n", $this->_plugin->getResponse()->getBody());
    }
    
    public function testServesRequestsFromTheCacheAndProcessEsiTagsWithAlternativeSource()
    {
        $cacheStub = $this->getMock('\Zend\Cache\Frontend\Core', array('load'));
        $cacheStub->expects($this->once())
                  ->method('load')
                  ->with($this->equalTo(md5('/test')))
                  ->will($this->returnValue('<esi:include src="http://example.com/esi/fail" alt="http://example.com/esi/alt" />'));
        
        $request = new Controller\Request\Http('http://example.com/test');
        $response = new Controller\Response\Cli();
        
        $this->_plugin = new Controller\Plugin\HttpGatewayCache($cacheStub);
        $this->_plugin->setResponse($response)
                      ->dispatchLoopStartup($request);
        
        $this->assertEquals("alt processed esi content\n", $this->_plugin->getResponse()->getBody());
    }
    
    public function testServesRequestsFromTheCacheAndProcessEsiTagsWithFailingAlternativeSourceAndWithoutOnErrorAttribute()
    {
        $cacheStub = $this->getMock('\Zend\Cache\Frontend\Core', array('load'));
        $cacheStub->expects($this->once())
                  ->method('load')
                  ->with($this->equalTo(md5('/test')))
                  ->will($this->returnValue('<esi:include src="http://example.com/esi/fail" alt="http://example.com/esi/fail" />'));
        
        $request = new Controller\Request\Http('http://example.com/test');
        $response = new Controller\Response\Cli();
        
        $this->_plugin = new Controller\Plugin\HttpGatewayCache($cacheStub);
        $this->_plugin->setResponse($response)
                      ->dispatchLoopStartup($request);
        
        $this->assertEquals('<esi:include src="http://example.com/esi/fail" alt="http://example.com/esi/fail" />', $this->_plugin->getResponse()->getBody());
    }
    
    public function testServesRequestsFromTheCacheAndProcessEsiTagsWithFailingAlternativeSourceAndWithOnErrorAttribute()
    {
        $cacheStub = $this->getMock('\Zend\Cache\Frontend\Core', array('load'));
        $cacheStub->expects($this->once())
                  ->method('load')
                  ->with($this->equalTo(md5('/test')))
                  ->will($this->returnValue('Normal content<esi:include src="http://example.com/esi/fail" alt="http://example.com/esi/fail" onerror="continue" />'));
        
        $request = new Controller\Request\Http('http://example.com/test');
        $response = new Controller\Response\Cli();
        
        $this->_plugin = new Controller\Plugin\HttpGatewayCache($cacheStub);
        $this->_plugin->setResponse($response)
                      ->dispatchLoopStartup($request);
        
        $this->assertEquals('Normal content', $this->_plugin->getResponse()->getBody());
    }
    
    public function testHitsTheCacheWhenServingNonCachedResponses()
    {
        $cacheStub = $this->getMock('\Zend\Cache\Frontend\Core', array('load', 'save'));
        $cacheStub->expects($this->once())
                  ->method('load')
                  ->with($this->equalTo(md5('/esi')))
                  ->will($this->returnValue(false));
        
        $cacheStub->expects($this->once())
                  ->method('save')
                  ->with($this->equalTo("processed esi content\n"), $this->equalTo(md5('/esi')), $this->equalTo(array()), 30);
        
        $request = new Controller\Request\Http('http://example.com/esi');
        $response = new Controller\Response\Cli();
        $response->setHeader('Cache-control', 'max-age=30');
        
        $this->_plugin = new Controller\Plugin\HttpGatewayCache($cacheStub);
        Controller\Front::getInstance()->registerPlugin($this->_plugin)
                                       ->dispatch($request, $response);
        
        $this->assertEquals("processed esi content\n", $this->_plugin->getResponse()->getBody());
    }
}