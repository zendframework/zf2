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
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @namespace
 */
namespace Zend\Uri;

use Zend\Validator;

/**
 * Generic URI handler
 *
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Uri
{
    /**
     * Character classes defined in RFC-3986
     */
    const CHAR_UNRESERVED = '\w\-\.~';
    const CHAR_GEN_DELIMS = ':\/\?#\[\]@';
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';
    const CHAR_RESERVED   = ':\/\?#\[\]@!\$&\'\(\)\*\+,;=';

    /**
     * Regular expression matching pattern defined in RFC-3986, Appendix B
     */
    const URI_PATTERN = '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?';
    const URI_PATTERN_DELIMITED = '|^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?|';

    /**
     * URI scheme
     *
     * @var string
     */
    protected $scheme;

    /**
     * URI userInfo part (usually user:password in HTTP URLs)
     *
     * @var string
     */
    protected $userInfo;

    /**
     * URI hostname
     *
     * @var string
     */
    protected $host;

    /**
     * URI port
     *
     * @var integer
     */
    protected $port;

    /**
     * URI path
     *
     * @var string
     */
    protected $path;

    /**
     * URI query string
     *
     * @var string
     */
    protected $query;

    /**
     * URI fragment
     *
     * @var string
     */
    protected $fragment;

    /**
     * Array of valid schemes.
     *
     * Subclasses of this class that only accept specific schemes may set the
     * list of accepted schemes here. If not empty, when setScheme() is called
     * it will only accept the schemes listed here.
     *
     * @var array
     */
    protected static $validSchemes = array();

    /**
     * List of default ports per scheme
     *
     * Inheriting URI classes may set this, and the normalization methods will
     * automatically remove the port if it is equal to the default port for the
     * current scheme
     *
     * @var array
     */
    protected static $defaultPorts = array();

    /**
     * Create a new URI object
     *
     * @param  \Zend\Uri\Uri|string|null $uri
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($uri = null)
    {
        if (is_string($uri)) {
            $this->parse($uri);
        } elseif ($uri instanceof self) {
            // Copy constructor
            $this->setFromUri($uri);
        } elseif ($uri !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expecting a string or a URI object, received "%s"',
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }
    }

    /**
     * Check if the URI is valid
     *
     * Note that a relative URI may still be valid
     *
     * @return boolean
     */
    public function isValid()
    {
        if ($this->host) {
            if (strlen($this->path) > 0 && substr($this->path, 0, 1) != '/') {
                return false;
            }
            return true;
        }

        if ($this->userInfo || $this->port) {
            return false;
        }

        if ($this->path) {
            // Check path-only (no host) URI
            if (substr($this->path, 0, 2) == '//') {
                return false;
            }
            return true;
        }

        if (! ($this->query || $this->fragment)) {
            // No host, path, query or fragment - this is not a valid URI
            return false;
        }

        return true;
    }

    /**
     * Check if the URI is an absolute or relative URI
     *
     * @return boolean
     */
    public function isAbsolute()
    {
        return ($this->scheme !== null);
    }

    /**
     * Parse a authority string into the fragments of this uri
     *
     * @param  string $authority
     */
    protected function parseAuthority($authority)
    {
        // capture userInfo
        // The userInfo can also contain '@' symbols; use rightmost
        $atPos = strrpos($authority, '@');
        if ($atPos !== false) {
            $userInfo = substr($authority, 0, $atPos);
            $this->setUserInfo($userInfo);
            $authority = substr($authority, $atPos + 1);
        }

        // capture port
        $colonPos = strrpos($authority, ':');
        if ($colonPos !== false) {
            $port = substr($authority, $colonPos + 1);
            if ($port) {
                $this->setPort((int) $port);
            }
            $authority = substr($authority, 0, $colonPos);
        }

        // only the host remains.
        $this->setHost($authority);
    }

    /**
     * Parse a URI string into the fragments of this uri
     *
     * @param  string $uri
     */
    protected function parse($uri)
    {
        if (!preg_match(self::URI_PATTERN_DELIMITED, $uri, $match)) {
            // non-matching strings set no uri parts
            return;
        }

        // See RFC-3986, Appendix B for a guide to the resulting subexpressions

        if (!empty($match[2])) {
            $this->setScheme($match[2]);
        }

        if (!empty($match[3])) {
            // subexpression 3 determines the authority exists
            // subexpression 4 is the actual authority value
            $this->parseAuthority($match[4]);
        }
        
        if (!empty($match[5])) {
            $this->setPath($match[5]);
        }
        if (!empty($match[7])) {
            $this->setQuery($match[7]);
        }
        if (!empty($match[9])) {
            $this->setFragment($match[9]);
        }
    }

    /**
     * Compose the URI into a string
     *
     * @return string
     */
    public function toString()
    {
        if (!$this->isValid()) {
            throw new Exception\InvalidUriException('URI is not valid and cannot be converted into a string');
        }

        $uri = '';

        if ($this->scheme) {
            $uri .= $this->scheme . ':';
        }

        if ($this->host !== null) {
            $uri .= '//';
            if ($this->userInfo) {
                $uri .= $this->userInfo . '@';
            }
            $uri .= $this->host;
            if ($this->port) {
                $uri .= ':' . $this->port;
            }
        }

        if ($this->path) {
            $uri .= $this->path;
        } elseif ($this->host && ($this->query || $this->fragment)) {
            $uri .= '/';
        }

        if ($this->query) {
            $uri .= "?" . $this->query;
        }

        if ($this->fragment) {
            $uri .= "#" . $this->fragment;
        }

        return $uri;
    }

    /**
     * Normalize the URI
     *
     * Normalizing a URI includes removing any redundant parent directory or
     * current directory references from the path (e.g. foo/bar/../baz becomes
     * foo/baz), normalizing the scheme case, decoding any over-encoded
     * characters etc.
     *
     * Eventually, two normalized URLs pointing to the same resource should be
     * equal even if they were originally represented by two different strings
     *
     * @return Uri
     */
    public function normalize()
    {
        if ($this->scheme) {
            $this->scheme = static::normalizeScheme($this->scheme);
        }

        if ($this->host) {
            $this->host = static::normalizeHost($this->host);
        }

        if ($this->port) {
            $this->port = static::normalizePort($this->port, $this->scheme);
        }

        if ($this->path) {
            $this->path = static::normalizePath($this->path);
        }

        if ($this->query) {
            $this->query = static::normalizeQuery($this->query);
        }

        if ($this->fragment) {
            $this->fragment = static::normalizeFragment($this->fragment);
        }

        // If path is empty (and we have a host), path should be '/'
        if ($this->host && empty($this->path)) {
            $this->path = '/';
        }

        return $this;
    }

    /**
     * Convert a relative URI into an absolute URI using a base absolute URI as
     * a reference.
     *
     * This is similar to merge() - only it uses the supplied URI as the
     * base reference instead of using the current URI as the base reference.
     *
     * Merging algorithm is adapted from RFC-3986 section 5.2
     * (@link http://tools.ietf.org/html/rfc3986#section-5.2)
     *
     * @param  Uri|string $baseUri
     * @return Uri
     */
    public function resolve($baseUri)
    {
        // Ignore if URI is absolute
        if ($this->isAbsolute()) {
            return $this;
        }

        if (is_string($baseUri)) {
            $baseUri = new static($baseUri);
        }

        if (!$baseUri instanceof static) {
            throw new Exception\InvalidUriTypeException(sprintf(
                'Provided base URL is not an instance of "%s"',
                get_class($this)
            ));
        }

        // Merging starts here...
        if ($this->getHost()) {
            $this->setPath(static::removePathDotSegments($this->getPath()));
        } else {
            $basePath = $baseUri->getPath();
            $relPath  = $this->getPath();
            if (!$relPath) {
                $this->setPath($basePath);
                if (!$this->getQuery()) {
                    $this->setQuery($baseUri->getQuery());
                }
            } else {
                if (substr($relPath, 0, 1) == '/') {
                    $this->setPath(static::removePathDotSegments($relPath));
                } else {
                    if ($baseUri->getHost() && !$basePath) {
                        $mergedPath = '/';
                    } else {
                        $mergedPath = substr($basePath, 0, strrpos($basePath, '/') + 1);
                    }
                    $this->setPath(static::removePathDotSegments($mergedPath . $relPath));
                }
            }

            // Set the authority part
            $this->setUserInfo($baseUri->getUserInfo());
            $this->setHost($baseUri->getHost());
            $this->setPort($baseUri->getPort());
        }

        $this->setScheme($baseUri->getScheme());
        return $this;
    }


    /**
     * Convert the link to a relative link by substracting a base URI
     *
     *  This is the opposite of resolving a relative link - i.e. creating a
     *  relative reference link from an original URI and a base URI.
     *
     *  If the two URIs do not intersect (e.g. the original URI is not in any
     *  way related to the base URI) the URI will not be modified.
     *
     * @param  Uri|string $baseUri
     * @return Uri
     */
    public function makeRelative($baseUri)
    {
        // Copy base URI, we should not modify it
        $baseUri = new static($baseUri);

        $this->normalize();
        $baseUri->normalize();

        $host     = $this->getHost();
        $baseHost = $baseUri->getHost();
        if ($host && $baseHost && ($host != $baseHost)) {
            // Not the same hostname
            return $this;
        }

        $port     = $this->getPort();
        $basePort = $baseUri->getPort();
        if ($port && $basePort && ($port != $basePort)) {
            // Not the same port
            return $this;
        }

        $scheme     = $this->getScheme();
        $baseScheme = $baseUri->getScheme();
        if ($scheme && $baseScheme && ($scheme != $baseScheme)) {
            // Not the same scheme (e.g. HTTP vs. HTTPS)
            return $this;
        }

        // Remove host, port and scheme
        $this->setHost(null)
             ->setPort(null)
             ->setScheme(null);

        // Is path the same?
        if ($this->getPath() == $baseUri->getPath()) {
            $this->setPath('');
            return $this;
        }

        $pathParts = preg_split('|(/)|', $this->getPath(),    null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $baseParts = preg_split('|(/)|', $baseUri->getPath(), null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Get the intersection of existing path parts and those from the
        // provided URI
        $matchingParts = array_intersect_assoc($pathParts, $baseParts);

        // Loop through the matches
        foreach ($matchingParts as $index => $segment) {
            // If we skip an index at any point, we have parent traversal, and
            // need to prepend the path accordingly
            if ($index && !isset($matchingParts[$index - 1])) {
                array_unshift($pathParts, '../');
                continue;
            }

            // Otherwise, we simply unset the given path segment
            unset($pathParts[$index]);
        }

        // Reset the path by imploding path segments
        $this->setPath(implode($pathParts));

        return $this;
    }

    /**
     * Get the scheme part of the URI
     *
     * @return string|null
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Get the User-info (usually user:password) part
     *
     * @return string|null
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Get the URI host
     *
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get the URI port
     *
     * @return integer|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get the URI path
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the URI query
     *
     * @return string|null
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Return the query string as an associative array of key => value pairs
     *
     * This is an extension to RFC-3986 but is quite useful when working with
     * most common URI types
     *
     * @return array
     */
    public function getQueryAsArray()
    {
        $query = array();
        if ($this->query) {
            parse_str($this->query, $query);
        }

        return $query;
    }

    /**
     * Get the URI fragment
     *
     * @return string|null
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Set the URI scheme
     *
     * If the scheme is not valid according to the generic scheme syntax or
     * is not acceptable by the specific URI class (e.g. 'http' or 'https' are
     * the only acceptable schemes for the Zend\Uri\Http class) an exception
     * will be thrown.
     *
     * You can check if a scheme is valid before setting it using the
     * validateScheme() method.
     *
     * @param  string $scheme
     * @throws Exception\InvalidUriPartException
     * @return Uri
     */
    public function setScheme($scheme)
    {
        if (($scheme !== null) && (!static::validateScheme($scheme))) {
            throw new Exception\InvalidUriPartException(sprintf(
                'Scheme "%s" is not valid or is not accepted by %s',
                $scheme,
                get_class($this)
            ), Exception\InvalidUriPartException::INVALID_SCHEME);
        }

        $this->scheme = $scheme;
        return $this;
    }

    /**
     * Set the URI User-info part (usually user:password)
     *
     * @param  string $userInfo
     * @return Uri
     */
    public function setUserInfo($userInfo)
    {
        $this->userInfo = $userInfo;
        return $this;
    }

    /**
     * Set the URI host
     *
     * Note that the generic syntax for URIs allows using host names which
     * are not neceserily IPv4 addresses or valid DNS host names. For example,
     * IPv6 addresses are allowed as well, and also an abstract "registered name"
     * which may be any name composed of a valid set of characters, including,
     * for example, tilda (~) and underscore (_) which are not allowed in DNS
     * names.
     *
     * Subclasses of Uri may impose more strict validation of host names - for
     * example the HTTP RFC clearly states that only IPv4 and valid DNS names
     * are allowed in HTTP URIs.
     *
     * @param  string $host
     * @return Uri
     */
    public function setHost($host)
    {
        if (($host !== '')
            && ($host !== null)
            && !static::validateHost($host)
        ) {
            throw new Exception\InvalidUriPartException(sprintf(
                'Host "%s" is not valid or is not accepted by %s',
                $host,
                get_class($this)
            ), Exception\InvalidUriPartException::INVALID_HOSTNAME);
        }

        $this->host = $host;
        return $this;
    }

    /**
     * Set the port part of the URI
     *
     * @param  integer $port
     * @return Uri
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Set the path
     *
     * @param  string $path
     * @return Uri
     */
    public function setPath($path)
    {
        if ($path !== null) {
            $path = static::encodePath($path);
        }
        $this->path = $path;
        return $this;
    }

    /**
     * Set the query string
     *
     * If an array is provided, will encode this array of parameters into a
     * query string. Array values will be represented in the query string using
     * PHP's common square bracket notation.
     *
     * @param  string|array $query
     * @return Uri
     */
    public function setQuery($query)
    {
        if (is_array($query)) {
            // We replace the + used for spaces by http_build_query with the
            // more standard %20.
            $query = str_replace('+', '%20', http_build_query($query));
        }
        if ($query !== null) {
            $query = static::encodeQueryFragment($query);
        }
        $this->query = $query;
        return $this;
    }

    /**
     * Set the URI fragment part
     *
     * @param  string $fragment
     * @return Uri
     */
    public function setFragment($fragment)
    {
        if ($fragment !== null) {
            $fragment = static::encodeQueryFragment($fragment);
        }
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * Sets this Uri to match the Uri passed
     *
     * @param  \Zend\Uri\Uri $uri
     * @return Uri
     */
    public function setFromUri(Uri $uri)
    {
        $this->setScheme($uri->getScheme());
        $this->setUserInfo($uri->getUserInfo());
        $this->setHost($uri->getHost());
        $this->setPort($uri->getPort());
        $this->setPath($uri->getPath());
        $this->setQuery($uri->getQuery());
        $this->setFragment($uri->getFragment());
        return $this;
    }

    /**
     * Sets this Uri to match Uri string representation
     *
     * @param  string $uri
     * @return Uri
     * @throws Exception\InvalidArgumentException
     */
    public function setFromString($uri)
    {
        if (!is_string($uri)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expecting a string, received "%s"',
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }

        $this->clear();
        $this->parse($uri);
        return $this;
    }

    /**
     * Clear all the parts of this Uri
     *
     * @return Uri
     */
    public function clear()
    {
        $this->setScheme(null);
        $this->setUserInfo(null);
        $this->setHost(null);
        $this->setPort(null);
        $this->setPath(null);
        $this->setQuery(null);
        $this->setFragment(null);
        return $this;
    }

    /**
     * Magic method to convert the URI to a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->toString();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Encoding and Validation Methods
     */

    /**
     * Check if a scheme is valid or not
     *
     * Will check $scheme to be valid against the generic scheme syntax defined
     * in RFC-3986. If the class also defines specific acceptable schemes, will
     * also check that $scheme is one of them.
     *
     * @param  string $scheme
     * @return boolean
     */
    public static function validateScheme($scheme)
    {
        if (!empty(static::$validSchemes)
            && !in_array(strtolower($scheme), static::$validSchemes)
        ) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z][A-Za-z0-9\-\.+]*$/', $scheme);
    }

    /**
     * Check that the userInfo part of a URI is valid
     *
     * @param  string $userInfo
     * @return boolean
     */
    public static function validateUserInfo($userInfo)
    {
        $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':]+|%[A-Fa-f0-9]{2})*$/';
        return (boolean) preg_match($regex, $userInfo);
    }

    /**
     * Validate the host part
     *
     * Note that the generic URI syntax allows different host representations,
     * including IPv4 addresses, IPv6 addresses and future IP address formats
     * enclosed in square brackets, and registered names which may be DNS names
     * or even more complex names. This is different (and is much more loose)
     * from what is commonly accepted as valid HTTP URLs for example.
     *
     * @param  string  $host
     * @param  integer $allowed bitmask of allowed host types
     * @return boolean
     */
    public static function validateHost($host)
    {
        if (strncmp($host, '[', 1) == 0) {
            return static::isValidIpLiteral($host);
        }

        // from RFC 3986: If host matches the rule for IPv4address, then it 
        // should be considered an IPv4 address literal and not a reg-name.    
        if (static::isValidIpV4Address($host)) {
            return true;
        }
        
        return static::isValidRegName($host);
    }

    /**
     * Validate the port
     *
     * Valid values include numbers between 1 and 65535, and empty values
     *
     * @param  integer $port
     * @return boolean
     */
    public static function validatePort($port)
    {
        if ($port === 0) {
            return false;
        }

        if ($port) {
            $port = (int) $port;
            if ($port < 1 || $port > 0xffff) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate the path
     *
     * @param  string $path
     * @return boolean
     */
    public static function validatePath($path)
    {
        $pchar   = '(?:[' . self::CHAR_UNRESERVED . ':@&=\+\$,]+|%[A-Fa-f0-9]{2})*';
        $segment = $pchar . "(?:;{$pchar})*";
        $regex   = "/^{$segment}(?:\/{$segment})*$/";
        return (boolean) preg_match($regex, $path);
    }

    /**
     * Check if a URI query or fragment part is valid or not
     *
     * Query and Fragment parts are both restricted by the same syntax rules,
     * so the same validation method can be used for both.
     *
     * You can encode a query or fragment part to ensure it is valid by passing
     * it through the encodeQueryFragment() method.
     *
     * @param  string $input
     * @return boolean
     */
    public static function validateQueryFragment($input)
    {
        $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':@\/\?]+|%[A-Fa-f0-9]{2})*$/';
        return (boolean) preg_match($regex, $input);
    }

    /**
     * URL-encode the user info part of a URI
     *
     * @param  string $userInfo
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public static function encodeUserInfo($userInfo)
    {
        if (!is_string($userInfo)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expecting a string, got %s',
                (is_object($userInfo) ? get_class($userInfo) : gettype($userInfo))
            ));
        }

        $regex   = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:]|%(?![A-Fa-f0-9]{2}))/';
        $replace = function($match) {
            return rawurlencode($match[0]);
        };

        return preg_replace_callback($regex, $replace, $userInfo);
    }

    /**
     * Encode the path
     *
     * Will replace all characters which are not strictly allowed in the path
     * part with percent-encoded representation
     *
     * @param  string $path
     * @return string
     */
    public static function encodePath($path)
    {
        if (!is_string($path)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expecting a string, got %s',
                (is_object($path) ? get_class($path) : gettype($path))
            ));
        }

        $regex   = '/(?:[^' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/';
        $replace = function($match) {
            return rawurlencode($match[0]);
        };

        return preg_replace_callback($regex, $replace, $path);
    }

    /**
     * URL-encode a query string or fragment based on RFC-3986 guidelines.
     *
     * Note that query and fragment encoding allows more unencoded characters
     * than the usual rawurlencode() function would usually return - for example
     * '/' and ':' are allowed as literals.
     *
     * @param  string $input
     * @return string
     */
    public static function encodeQueryFragment($input)
    {
        if (!is_string($input)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expecting a string, got %s',
                (is_object($input) ? get_class($input) : gettype($input))
            ));
        }

        $regex   = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/';
        $replace = function($match) {
            return rawurlencode($match[0]);
        };

        return preg_replace_callback($regex, $replace, $input);
    }

    /**
     * Remove any extra dot segments (/../, /./) from a path
     *
     * Algorithm is adapted from RFC-3986 section 5.2.4
     * (@link http://tools.ietf.org/html/rfc3986#section-5.2.4)
     *
     * @todo   consider optimizing
     *
     * @param  string $path
     * @return string
     */
    public static function removePathDotSegments($path)
    {
        $output = '';

        while ($path) {
            if ($path == '..' || $path == '.') {
                break;
            }

            switch (true) {
                case ($path == '/.'):
                    $path = '/';
                    break;
                case ($path == '/..'):
                    $path   = '/';
                    $output = substr($output, 0, strrpos($output, '/', -1));
                    break;
                case (substr($path, 0, 4) == '/../'):
                    $path   = '/' . substr($path, 4);
                    $output = substr($output, 0, strrpos($output, '/', -1));
                    break;
                case (substr($path, 0, 3) == '/./'):
                    $path = substr($path, 2);
                    break;
                case (substr($path, 0, 2) == './'):
                    $path = substr($path, 2);
                    break;
                case (substr($path, 0, 3) == '../'):
                    $path = substr($path, 3);
                    break;
                default:
                    $slash = strpos($path, '/', 1);
                    if ($slash === false) {
                        $seg = $path;
                    } else {
                        $seg = substr($path, 0, $slash);
                    }

                    $output .= $seg;
                    $path    = substr($path, strlen($seg));
                    break;
            }
        }

        return $output;
    }

    /**
     * Merge a base URI and a relative URI into a new URI object
     *
     * This convenience method wraps ::resolve() to allow users to quickly
     * create new absolute URLs without the need to instantiate and clone
     * URI objects.
     *
     * If objects are passed in, none of the passed objects will be modified.
     *
     * @param  Uri|string $baseUri
     * @param  Uri|string $relativeUri
     * @return Uri
     */
    public static function merge($baseUri, $relativeUri)
    {
        $uri = new static($relativeUri);
        return $uri->resolve($baseUri);
    }

    /**
     * Validates an IPv4 address
     *
     * @param string $value
     */
    protected static function isValidIpV4Address($ip)
    {
        $ip2long = ip2long($ip);
        if($ip2long === false) {
            return false;
        }

        return $ip == long2ip($ip2long);
    }

    /**
     * Check if a host name is a valid IP Literal
     *
     * @param  string  $host
     * @return boolean
     */
    protected static function isValidIpLiteral($ip)
    {
        // IPvFuture is allowed syntactically
        $regex = '/^v\.[[:xdigit:]]+[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':]+$/';
        if (preg_match($regex, $ip)) {
            return true;
        }

        if (!preg_match('/^\[(.+)\]$/', $ip, $match)) {
            return false;
        }
        $ip = $match[1];

        $validatorParams = array(
            'allowipv4' => false,
            'allowipv6' => true,
        );

        $validator = new Validator\Ip($validatorParams);

        return $validator->isValid($ip);
    }

    /**
     * Check if an address is a valid registerd name (as defined by RFC-3986) address
     *
     * @param  string $host
     * @return boolean
     */
    protected static function isValidRegName($host)
    {
        $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':@\/\?]+|%[A-Fa-f0-9]{2})+$/';
        return (bool) preg_match($regex, $host);
    }

    /**
     * Part normalization methods
     *
     * These are called by normalize() using static::_normalize*() so they may
     * be extended or overridden by extending classes to implement additional
     * scheme specific normalization rules
     */

    /**
     * Normalize the scheme
     *
     * Usually this means simpy converting the scheme to lower case
     *
     * @param  string $scheme
     * @return string
     */
    protected static function normalizeScheme($scheme)
    {
        return strtolower($scheme);
    }

    /**
     * Normalize the host part
     *
     * By default this converts host names to lower case
     *
     * @param  string $host
     * @return string
     */
    protected static function normalizeHost($host)
    {
        return strtolower($host);
    }

    /**
     * Normalize the port
     *
     * If the class defines a default port for the current scheme, and the
     * current port is default, it will be unset.
     *
     * @param  integer $port
     * @param  string  $scheme
     * @return integer|null
     */
    protected static function normalizePort($port, $scheme = null)
    {
        if ($scheme
            && isset(static::$defaultPorts[$scheme])
            && ($port == static::$defaultPorts[$scheme])
        ) {
            return null;
        }

        return $port;
    }

    /**
     * Normalize the path
     *
     * This involves removing redundant dot segments, decoding any over-encoded
     * characters and encoding everything that needs to be encoded and is not
     *
     * @param  string $path
     * @return string
     */
    protected static function normalizePath($path)
    {
        $path = self::encodePath(
            self::decodeUrlEncodedChars(
                self::removePathDotSegments($path),
                '/[' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]/'
            )
        );

        return $path;
    }

    /**
     * Normalize the query part
     *
     * This involves decoding everything that doesn't need to be encoded, and
     * encoding everything else
     *
     * @param  string $query
     * @return string
     */
    protected static function normalizeQuery($query)
    {
        $query = self::encodeQueryFragment(
            self::decodeUrlEncodedChars(
                $query,
                '/[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]/'
            )
        );

        return $query;
    }

    /**
     * Normalize the fragment part
     *
     * Currently this is exactly the same as _normalizeQuery().
     *
     * @param  string $fragment
     * @return string
     */
    protected static function normalizeFragment($fragment)
    {
        return static::normalizeQuery($fragment);
    }

    /**
     * Decode all percent encoded characters which are allowed to be represented literally
     *
     * Will not decode any characters which are not listed in the 'allowed' list
     *
     * @param string $input
     * @param string $allowed Pattern of allowed characters
     */
    protected static function decodeUrlEncodedChars($input, $allowed = '')
    {
        $decodeCb = function($match) use ($allowed) {
            $char = rawurldecode($match[0]);
            if (preg_match($allowed, $char)) {
                return $char;
            }
            return $match[0];
        };

        return preg_replace_callback('/%[A-Fa-f0-9]{2}/', $decodeCb, $input);
    }
}
