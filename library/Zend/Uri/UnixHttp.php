<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Uri;

/**
 * A special URI type for specifying a resource path, a query or a fragment
 * to be used with unix sockets for local inter-process communication. The
 * URI for a unix socket is in fact a Zend\Uri\File but that can't be used
 * in Zend\Http\Client since the host would be empty while the path is
 * the one for the socket file.
 *
 * This class, however, interprets the path to the socket file as the
 * authority and thus this will be our host while you are allowed to
 * append another path to a web resource. Below is how a UnixHttp URI
 * is put together.
 *
 * <pre>
 *   unix:///var/run/my.sock:/over/there?slop=the#foobar
 *   \_/    \______________/ \_________/ \______/ \____/
 *    |           |               |          |       |
 *  scheme     authority         path      query  fragment
 * </pre>
 *
 * Other examples:
 *
 * <ul>
 *   <li><code>unix:///var/run/my.sock:?slop=the</code>, path to the web resource is empty, this defaults to '/'</li>
 *   <li><code>unix:///var/run/my.sock:#foobar</code>, path to the web resource is empty, this defaults to '/'</li>
 * </ul>
 */
class UnixHttp extends Http
{
    protected static $validSchemes = array('unix');

    protected $validHostTypes = self::HOST_ALL;

    public function isValid()
    {
        if (!$this->host) {
            return false;
        }

        return parent::isValid();
    }

    public function parse($uri)
    {
        $this->reset();

        // Capture scheme
        if (($scheme = self::parseScheme($uri)) !== null) {
            $this->setScheme($scheme);
            $uri = substr($uri, strlen($scheme) + 1);
        }

        // Capture authority part
        if (preg_match('|^//([^:\?#]*)|', $uri, $match)) {
            $authority = $match[1];
            $uri       = substr($uri, strlen($match[0]));
            $uri       = preg_replace('/^:/', '', $uri);
            $this->setHost($authority);
        }

        if (!$uri) {
            return $this;
        }

        // Capture the path
        if (preg_match('|^[^\?#]*|', $uri, $match)) {

            // shorten sequences of slashes or set default path if the
            // match is empty
            if ($match[0]) {
                $path = preg_replace('|/{1,}|', '/', $match[0]);
            } else {
                $path = '/';
            }

            $this->setPath($path);
            $uri = substr($uri, strlen($match[0]));
        }

        if (!$uri) {
            return $this;
        }

        // Capture the query
        if (preg_match('|^\?([^#]*)|', $uri, $match)) {
            $this->setQuery($match[1]);
            $uri = substr($uri, strlen($match[0]));
        }
        if (!$uri) {
            return $this;
        }

        // All that's left is the fragment
        if ($uri && substr($uri, 0, 1) == '#') {
            $this->setFragment(substr($uri, 1));
        }

        return $this;
    }

    /**
     * Compose the URI into a string
     *
     * @return string
     * @throws Exception\InvalidUriException
     */
    public function toString()
    {
        if (!$this->isValid()) {
            if ($this->isAbsolute() || !$this->isValidRelative()) {
                throw new Exception\InvalidUriException(
                    'URI is not valid and cannot be converted into a string'
                );
            }
        }

        $uri = '';

        if ($this->scheme) {
            $uri .= $this->scheme . ':';
        }

        if ($this->host !== null) {
            $uri .= '//';
            $uri .= $this->host;
        }

        if ($this->path) {
            $uri .= static::encodePath($this->path);
        } elseif ($this->host && ($this->query || $this->fragment)) {
            $uri .= ':/';
        }

        if ($this->query) {
            $uri .= "?" . static::encodeQueryFragment($this->query);
        }

        if ($this->fragment) {
            $uri .= "#" . static::encodeQueryFragment($this->fragment);
        }

        return $uri;
    }
} 
