<?php

namespace Zend\Controller\Plugin;

/**
 * A reverse proxy cache written on top of Zend Framework
 *
 * @author Christian Soronellas <christian@sistemes-cayman.es>
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
    public function __construct(Zend\Cache\Frontend $cache)
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
                    $lifetime = (int) sscanf(trim($header['value']), 'max-age=%d');
                    $this->_cache->save($content, $this->_getCacheKey($request), array(), $lifetime);
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
        if (strstr($content, '<esi:') !== false) {
            $xml = simplexml_import_dom(\DOMDocument::loadHTML($content));
            $xml->registerXPathNamespace('esi', 'http://www.edge-delivery.org/esi/1.0');
            
            // Check for esi includes, excluding those that its parent is another esi tag
            foreach ($xml->xpath('//esi:include') as $esiInclude) {
                // Check if this esi include isn't inside another esi
                if (!current($esiInclude->xpath('parent::node()[name()]'))->asXML() != 'esi:include') {
                    $attributes = $esiInclude->attributes();
                    
                    if (!empty($attributes['alt'])) {
                        $response = $this->_processInclude($attributes['src'], $attributes['alt']);
                    } else {
                        $response = $this->_processInclude($attributes['src']);
                    }
                    
                    // Check the response for any errors. If error and "onerror" attribute set
                    // to "continue" remove the esi tag
                    if (400 >= $response->getHTTPResponseCode()
                        && (!empty($attributes['onerror']) && 'continue' == $attributes['onerror'])
                    ) {
                        str_replace($esiInclude->asXML(), '', $content);
                    } elseif (400 < $response->getHTTPResponseCode()) {
                        str_replace($esiInclude->asXML(), $response->getBody(), $content);
                    }
                }
            }
            
            

            $this->getResponse()->setBody($content);
        }
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
        if (400 >= $response->getHTTPResponseCode()) {
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
        $frontController = Front::getInstance();
        $frontController->returnResponse(true);
        
        // Try to reach the the URI specified at src param
        $request = new Request\Http(parse_url($src, PHP_URL_PATH));
        $request->addHeader('Surrogate-Capability', 'zfcache="ZendHttpCacheGateway/1.0 ESI/1.0"');
        $response = $frontController->dispatch($request);
        $frontController->returnResponse(false);
        
        return $response;
    }
}