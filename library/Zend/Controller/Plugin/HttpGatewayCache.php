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
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Plugin;
use Zend\Controller,
    Zend\Controller\Request,
    Zend\Controller\Response;

/**
 * A reverse proxy cache written on top of Zend Framework
 *
 * @uses       \Zend\Controller\Plugin\AbstractPlugin
 * @uses       \Zend\Cache\Frontend
 * @uses       \Zend\Controller\Request\AbstractRequest
 * @uses       \Zend\Controller\Response\AbstractResponse
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HttpGatewayCache extends AbstractPlugin
{
    /**
     * The cache object instance
     * @var Zend\Cache\Frontend
     */
    protected $_cache;
    
    /**
     * A flag for whether the response comes already cached
     * @var boolean
     */
    protected $_responseCached = false;

    /**
     * Class constructor
     */
    public function __construct(\Zend\Cache\Frontend $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Fired when the dispatch loop starts. Checks if the response is already cached.
     */
    public function dispatchLoopStartup(Request\AbstractRequest $request)
    {   
        if (false !== ($data = $this->_cache->load($this->_getCacheKey($request)))) {
            $this->_process($data);
            $this->_responseCached = true;
            $request->setDispatched(true);
        }
    }
    
    /**
     * Fired when the dispatch loop has finished. Caches the response.
     */
    public function dispatchLoopShutdown()
    {
        // Here we have a full generated response, so replace ESI parts,
        // cache the response, and go on.
        if (!$this->_responseCached) {
            $content = $this->getResponse()->getBody();
            
            // Get the "Cache-control" header and extract the value. This
            // value will be the cached content lifetime. If no "Cache-control"
            // header set, the content won't be cached.
            foreach ($this->getResponse()->getHeaders() as $header) {
                if ('cache-control' == strtolower($header['name'])) {
                    // Extract the value, cache the content and go sleep! :)
                    $lifetime = sscanf(trim($header['value']), 'max-age=%d');
                    $lifetime = (int) $lifetime[0];
                    $this->_cache->save($content, $this->_getCacheKey($this->getRequest()), array(), $lifetime);
                }
            }

            $this->_process($content);
        }
    }
    
    /**
     * Generates a cache key using the Request's path info
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    protected function _getCacheKey(Request\AbstractRequest $request)
    {
        return md5($request->getPathInfo());
    }

    /**
     * Search for any esi include, and replace by its content
     */
    protected function _process($content)
    {
        $matches = array();
        if (preg_match_all('#<esi:include\ssrc="([^"]*)"(\salt="([^"]*)")?(\sonerror="([^"]*)")?\s?/>#i', $content, $matches) > 0) {
            for ($i = 0; $i < sizeof($matches[1]); $i++) {
                if (!empty($matches[3][$i])) {
                    $response = $this->_processInclude($matches[1][$i], $matches[3][$i]);
                } else {
                    $response = $this->_processInclude($matches[1][$i]);
                }
                
                // Check the response for any errors. If error and "onerror" attribute set
                // to "continue" remove the esi tag
                if ($response->getHTTPResponseCode() >= 400
                    && (!empty($matches[5][$i]) && 'continue' == $matches[5][$i])
                ) {
                    $content = str_replace($matches[0][$i], '', $content);
                } elseif ($response->getHTTPResponseCode() < 400) {
                    $content = str_replace($matches[0][$i], $response->getBody(), $content);
                }
            }
        }
        
        $this->getResponse()->setBody($content);
    }
    
    /**
     * Try to process an ESI include by performing an internal request.
     * Optionally it accepts an alternative URI for the cases when the
     * first try returns an error.
     * 
     * @param string $src
     * @param string $alt
     * @return Zend\Controller\Response\Http
     */
    protected function _processInclude($src, $alt = null)
    {
        $response = $this->_performInternalEsiRequest($src);
        
        // Check the response for any errors
        if ($response->getHTTPResponseCode() >= 400) {
            // Try to reach the alternate page if present
            if (null !== $alt) {
                $response = $this->_performInternalEsiRequest($alt);
            }
        }
        
        return $response;
    }
    
    /**
     * Performs an internal HTTP request.
     * 
     * @param string $uri 
     */
    protected function _performInternalEsiRequest($uri)
    {
        $frontController = Controller\Front::getInstance();
        
        // Try to reach the the URI specified at src param
        $request = new Request\Http($uri);
        $request->addHeader('Surrogate-Capability', 'zfcache="ZendHttpGatewayCache/1.0 ESI/1.0"');
        $response = new Response\Http();
        $frontController->setResponse($response);
        $frontController->dispatch($request);
        
        return $response;
    }
}