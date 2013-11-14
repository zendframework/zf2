<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Http;

use Zend\Stdlib\DispatchableInterface;
use Zend\Uri\Http;

/**
 * Http client Interface
 */
interface ClientInterface extends DispatchableInterface
{
    /**
     * Constructor
     *
     * @param string $uri
     * @param array|Traversable $options
     */
    public function __construct($uri = null, $options = null);

    /**
     * Set configuration parameters for this HTTP client
     *
     * @param  array|Traversable $options
     * @return Client
     * @throws Client\Exception\InvalidArgumentException
     */
    public function setOptions($options = array());

    /**
     * Load the connection adapter
     *
     * While this method is not called more than one for a client, it is
     * separated from ->request() to preserve logic and readability
     *
     * @param  Client\Adapter\AdapterInterface|string $adapter
     * @return Client
     * @throws Client\Exception\InvalidArgumentException
     */
    public function setAdapter($adapter);

    /**
     * Load the connection adapter
     *
     * @return Client\Adapter\AdapterInterface $adapter
     */
    public function getAdapter();

    /**
     * Set request
     *
     * @param Request $request
     * @return Client
     */
    public function setRequest(Request $request);

    /**
     * Get Request
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Set response
     *
     * @param Response $response
     * @return Client
     */
    public function setResponse(Response $response);

    /**
     * Get Response
     *
     * @return Response
     */
    public function getResponse();


    /**
     * Get the last request (as a string)
     *
     * @return string
     */
    public function getLastRawRequest();

    /**
     * Get the last response (as a string)
     *
     * @return string
     */
    public function getLastRawResponse();

    /**
     * Get the redirections count
     *
     * @return int
     */
    public function getRedirectionsCount();

    /**
     * Set Uri (to the request)
     *
     * @param string|Http $uri
     * @return Client
     */
    public function setUri($uri);

    /**
     * Get uri (from the request)
     *
     * @return Http
     */
    public function getUri();

    /**
     * Set the HTTP method (to the request)
     *
     * @param string $method
     * @return Client
     */
    public function setMethod($method);

    /**
     * Get the HTTP method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set the query string argument separator
     *
     * @param string $argSeparator
     * @return Client
     */
    public function setArgSeparator($argSeparator);

    /**
     * Get the query string argument separator
     *
     * @return string
     */
    public function getArgSeparator();

    /**
     * Set the encoding type and the boundary (if any)
     *
     * @param string $encType
     * @param string $boundary
     * @return Client
     */
    public function setEncType($encType, $boundary = null);

    /**
     * Get the encoding type
     *
     * @return string
     */
    public function getEncType();

    /**
     * Set raw body (for advanced use cases)
     *
     * @param string $body
     * @return Client
     */
    public function setRawBody($body);

    /**
     * Set the POST parameters
     *
     * @param array $post
     * @return Client
     */
    public function setParameterPost(array $post);

    /**
     * Set the GET parameters
     *
     * @param array $query
     * @return Client
     */
    public function setParameterGet(array $query);

    /**
     * Reset all the HTTP parameters (request, response, etc)
     *
     * @param  bool   $clearCookies  Also clear all valid cookies? (defaults to false)
     * @return Client
     */
    public function resetParameters($clearCookies = false);

    /**
     * Return the current cookies
     *
     * @return array
     */
    public function getCookies();

    /**
     * Add a cookie
     *
     * @param array|ArrayIterator|Header\SetCookie|string $cookie
     * @param string  $value
     * @param string  $expire
     * @param string  $path
     * @param string  $domain
     * @param  bool $secure
     * @param  bool $httponly
     * @param string  $maxAge
     * @param string  $version
     * @throws Exception\InvalidArgumentException
     * @return Client
     */
    public function addCookie($cookie, $value = null, $expire = null, $path = null, $domain = null, $secure = false, $httponly = true, $maxAge = null, $version = null);

    /**
     * Set an array of cookies
     *
     * @param  array $cookies
     * @throws Exception\InvalidArgumentException
     * @return Client
     */
    public function setCookies($cookies);

    /**
     * Clear all the cookies
     */
    public function clearCookies();

    /**
     * Set the headers (for the request)
     *
     * @param  Headers|array $headers
     * @throws Exception\InvalidArgumentException
     * @return Client
     */
    public function setHeaders($headers);

    /**
     * Check if exists the header type specified
     *
     * @param  string $name
     * @return bool
     */
    public function hasHeader($name);

    /**
     * Get the header value of the request
     *
     * @param  string $name
     * @return string|bool
     */
    public function getHeader($name);

    /**
     * Set streaming for received data
     *
     * @param string|bool $streamfile Stream file, true for temp file, false/null for no streaming
     * @return \Zend\Http\Client
     */
    public function setStream($streamfile = true);

    /**
     * Get status of streaming for received data
     * @return bool|string
     */
    public function getStream();

    /**
     * Create a HTTP authentication "Authorization:" header according to the
     * specified user, password and authentication method.
     *
     * @param string $user
     * @param string $password
     * @param string $type
     * @throws Exception\InvalidArgumentException
     * @return Client
     */
    public function setAuth($user, $password, $type = 'basic');

    /**
     * Clear http authentication
     */
    public function clearAuth();

    /**
     * Send HTTP request
     *
     * @param  Request $request
     * @return Response
     * @throws Exception\RuntimeException
     * @throws Client\Exception\RuntimeException
     */
    public function send(Request $request = null);

    /**
     * Fully reset the HTTP client (auth, cookies, request, response, etc.)
     *
     * @return Client
     */
    public function reset();

    /**
     * Set a file to upload (using a POST request)
     *
     * Can be used in two ways:
     *
     * 1. $data is null (default): $filename is treated as the name if a local file which
     * will be read and sent. Will try to guess the content type using mime_content_type().
     * 2. $data is set - $filename is sent as the file name, but $data is sent as the file
     * contents and no file is read from the file system. In this case, you need to
     * manually set the Content-Type ($ctype) or it will default to
     * application/octet-stream.
     *
     * @param  string $filename Name of file to upload, or name to save as
     * @param  string $formname Name of form element to send as
     * @param  string $data Data to send (if null, $filename is read and sent)
     * @param  string $ctype Content type to use (if $data is set and $ctype is
     *                null, will be application/octet-stream)
     * @return Client
     * @throws Exception\RuntimeException
     */
    public function setFileUpload($filename, $formname, $data = null, $ctype = null);

    /**
     * Remove a file to upload
     *
     * @param  string $filename
     * @return bool
     */
    public function removeFileUpload($filename);

    /**
     * Encode data to a multipart/form-data part suitable for a POST request.
     *
     * @param string $boundary
     * @param string $name
     * @param mixed $value
     * @param string $filename
     * @param array $headers Associative array of optional headers @example ("Content-Transfer-Encoding" => "binary")
     * @return string
     */
    public function encodeFormData($boundary, $name, $value, $filename = null, $headers = array());

    /**
     * Create a HTTP authentication "Authorization:" header according to the
     * specified user, password and authentication method.
     *
     * @see http://www.faqs.org/rfcs/rfc2617.html
     * @param string $user
     * @param string $password
     * @param string $type
     * @return string
     * @throws Client\Exception\InvalidArgumentException
     */
    public static function encodeAuthHeader($user, $password, $type = 'basic');
}
