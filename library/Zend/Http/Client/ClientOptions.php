<?php
namespace Zend\Http\Client;

use Zend\Stdlib\AbstractOptions;
use Zend\Http\Request;

class ClientOptions extends AbstractOptions
{
    /**
     * Maximum number of redirects to follow
     *
     * @var int
     */
    protected $maxRedirects = 5;

    /**
     * Whether to strictly follow the RFC when redirecting
     *
     * @var bool
     */
    protected $strictRedirects = false;

    /**
     * User agent identifier string sent in request headers
     *
     * @var string
     */
    protected $userAgent = 'Zend\Http\Client';

    /**
     * Connection timeout in seconds
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * Connection adapter class to use
     *
     * @var string
     */
    protected $adapter = 'Zend\Http\Client\Adapter\Socket';

    /**
     * User agent identifier string that is sent in the request headers
     *
     * @var string
     */
    protected $httpVersion = Request::VERSION_11;

    /**
     * Whether to store last response for later retrieval
     *
     * @var bool
     */
    protected $storeResponse = true;

    /**
     * Whether to enable keep-alive connections with the server.
     * Useful and might improve performance if several consecutive requests to the same server are performed.
     *
     * @var bool
     */
    protected $keepAlive = false;

    /**
     * The name of a file to stream output to, or false to disable
     *
     * @var string|bool
     */
    protected $outputStream = false;

    /**
     * Whether to pass the cookie value through urlencode/urldecode.
     * Enabling this breaks support with some web servers.
     * Disabling this limits the range of values the cookies can contain.
     *
     * @var bool
     */
    protected $encodeCookies = true;

    /**
     * Whether URIs need to strictly adhere to the RFC 3986 uri scheme
     *
     * @var bool
     */
    protected $rfc3986Strict = false;

    /**
     * @param $adapter
     * @return ClientOptions
     */
    public function setAdapter($adapter)
    {
        $this->adapter = (string)$adapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param $encodeCookies
     * @return ClientOptions
     */
    public function setEncodeCookies($encodeCookies)
    {
        $this->encodeCookies = (boolean)$encodeCookies;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEncodeCookies()
    {
        return $this->encodeCookies;
    }

    /**
     * @param $httpVersion
     * @return ClientOptions
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = (string)$httpVersion;
        return $this;
    }

    /**
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * @param $keepAlive
     * @return ClientOptions
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = (boolean)$keepAlive;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * @param $maxRedirects
     * @return ClientOptions
     */
    public function setMaxRedirects($maxRedirects)
    {
        $this->maxRedirects = (int)$maxRedirects;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRedirects()
    {
        return $this->maxRedirects;
    }

    /**
     * @param $outputStream
     * @return ClientOptions
     */
    public function setOutputStream($outputStream)
    {
        $this->outputStream = $outputStream;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * @param $rfc3986Strict
     * @return ClientOptions
     */
    public function setRfc3986Strict($rfc3986Strict)
    {
        $this->rfc3986Strict = (boolean)$rfc3986Strict;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRfc3986Strict()
    {
        return $this->rfc3986Strict;
    }

    /**
     * @param $storeResponse
     * @return ClientOptions
     */
    public function setStoreResponse($storeResponse)
    {
        $this->storeResponse = (boolean)$storeResponse;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getStoreResponse()
    {
        return $this->storeResponse;
    }

    /**
     * @param $strictRedirects
     * @return ClientOptions
     */
    public function setStrictRedirects($strictRedirects)
    {
        $this->strictRedirects = (boolean)$strictRedirects;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getStrictRedirects()
    {
        return $this->strictRedirects;
    }

    /**
     * @param $timeout
     * @return ClientOptions
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param $userAgent
     * @return ClientOptions
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string)$userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
