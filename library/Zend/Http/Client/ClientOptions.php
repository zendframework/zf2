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
}
