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
 * @package    Zend_Gdata
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\GData;

/**
 * Provides functionality to interact with Google data APIs
 * Subclasses exist to implement service-specific features
 *
 * As the Google data API protocol is based upon the Atom Publishing Protocol
 * (APP), Gdata functionality extends the appropriate Zend_Gdata_App classes
 *
 * @link http://code.google.com/apis/gdata/overview.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GData extends App
{

    /**
     * Service name for use with Google's authentication mechanisms
     *
     * @var string
     */
    const AUTH_SERVICE_NAME = 'xapi';

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    /**
     * Packages to search for classes when using magic __call method, in order.
     *
     * @var array
     */
    protected $_registeredPackages = array(
        '\Zend\GData\Kind',
        '\Zend\GData\Extension',
        '\Zend\GData\GBase',
        '\Zend\GData\App\Extension',
        '\Zend\GData\App'
        );

    /**
     * Namespaces used for Gdata data
     *
     * @var array
     */
    public static $namespaces = array(
        array('gd', 'http://schemas.google.com/g/2005', 1, 0),
        array('openSearch', 'http://a9.com/-/spec/opensearchrss/1.0/', 1, 0),
        array('openSearch', 'http://a9.com/-/spec/opensearch/1.1/', 2, 0),
        array('rss', 'http://blogs.law.harvard.edu/tech/rss', 1, 0)
    );

    /**
     * Client object used to communicate
     *
     * @var \Zend\GData\HttpClient
     */
    protected $_httpClient;

    /**
     * Client object used to communicate in static context
     *
     * @var \Zend\GData\HttpClient
     */
    protected static $_staticHttpClient = null;

    /**
     * Create Gdata object
     *
     * @param \Zend\Http\Client $client
     * @param string $applicationId The identity of the app in the form of
     *          Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        parent::__construct($client, $applicationId);
    }

    /**
     * Imports a feed located at $uri.
     *
     * @param  string $uri
     * @param  \Zend\Http\Client $client The client used for communication
     * @param  string $className The class which is used as the return type
     * @throws \Zend\GData\App\Exception
     * @return string|\Zend\GData\App\Feed Returns string only if the object
     *                                    mapping has been disabled explicitly
     *                                    by passing false to the
     *                                    useObjectMapping() function.
     */
    public static function import($uri, $client = null,
        $className='\Zend\GData\Feed')
    {
        $app = new GData($client);
        $requestData = $app->decodeRequest('GET', $uri);
        $response = $app->performHttpRequest($requestData['method'], $requestData['url']);

        $feedContent = $response->getBody();

        $feed = self::importString($feedContent, $className);
        if ($client != null) {
            $feed->setHttpClient($client);
        }
        return $feed;
    }

    /**
     * Retrieve feed as string or object
     *
     * @param mixed $location The location as string or \Zend\GData\Query
     * @param string $className The class type to use for returning the feed
     * @throws \Zend\GData\App\InvalidArgumentException
     * @return string|\Zend\GData\App\Feed Returns string only if the object
     *                                    mapping has been disabled explicitly
     *                                    by passing false to the
     *                                    useObjectMapping() function.
     */
    public function getFeed($location, $className = '\Zend\GData\Feed')
    {
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            throw new App\InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend\GData\Query');
        }
        return parent::getFeed($uri, $className);
    }

    /**
     * Retrieve entry as string or object
     *
     * @param mixed $location The location as string or \Zend\GData\Query
     * @throws \Zend\GData\App\InvalidArgumentException
     * @return string|\Zend\GData\App\Entry Returns string only if the object
     *                                     mapping has been disabled explicitly
     *                                     by passing false to the
     *                                     useObjectMapping() function.
     */
    public function getEntry($location, $className='\Zend\GData\Entry')
    {
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            throw new App\InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend\GData\Query');
        }
        return parent::getEntry($uri, $className);
    }

    /**
     * Performs a HTTP request using the specified method.
     *
     * Overrides the definition in the parent (Zend_Gdata_App)
     * and uses the Zend_Gdata_HttpClient functionality
     * to filter the HTTP requests and responses.
     *
     * @param string $method The HTTP method for the request -
     *                       'GET', 'POST', 'PUT', 'DELETE'
     * @param string $url The URL to which this request is being performed,
     *                    or null if found in $data
     * @param array $headers An associative array of HTTP headers
     *                       for this request
     * @param string $body The body of the HTTP request
     * @param string $contentType The value for the content type of the
     *                            request body
     * @param int $remainingRedirects Number of redirects to follow
     *                                if requests results in one
     * @return \Zend\Http\Response The response object
     */
    public function performHttpRequest($method, $url, $headers = array(), $body = null, $contentType = null, $remainingRedirects = null)
    {
        if ($this->_httpClient instanceof HttpClient) {
            $filterResult = $this->_httpClient->filterHttpRequest($method, $url, $headers, $body, $contentType);
            $method = $filterResult['method'];
            $url = $filterResult['url'];
            $body = $filterResult['body'];
            $headers = $filterResult['headers'];
            $contentType = $filterResult['contentType'];
            return $this->_httpClient->filterHttpResponse(parent::performHttpRequest($method, $url, $headers, $body, $contentType, $remainingRedirects));
        } else {
            return parent::performHttpRequest($method, $url, $headers, $body, $contentType, $remainingRedirects);
        }
    }

    /**
     * Determines whether service object is authenticated.
     *
     * @return boolean True if service object is authenticated, false otherwise.
     */
    public function isAuthenticated()
    {
        $client = parent::getHttpClient();
        if ($client->getClientLoginToken() ||
            $client->getAuthSubToken()) {
                return true;
        }

        return false;
    }

}
