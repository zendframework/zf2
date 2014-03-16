<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http\Literal;

use Zend\Framework\Route\Assemble\AssembleInterface;
use Zend\Framework\Route\Http\EventInterface;
use Zend\Mvc\Router\Exception;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Uri\Http as Uri;

class Literal implements AssembleInterface
{
    protected $route;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @param  string $route
     * @param  array  $defaults
     */
    public function __construct($route, array $defaults = array())
    {
        $this->route    = $route;
        $this->defaults = $defaults;
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed|string
     */
    public function assemble(array $params = array(), array $options = array())
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getAssembledParams()
    {
        return array();
    }

    /**
     * @param Uri $uri
     * @param null $pathOffset
     * @return null|RouteMatch
     */
    public function match(Uri $uri, $pathOffset = null)
    {
        $path = $uri->getPath();

        if ($pathOffset !== null) {
            if ($pathOffset >= 0 && strlen($path) >= $pathOffset && !empty($this->route)) {
                if (strpos($path, $this->route, $pathOffset) === $pathOffset) {
                    return new RouteMatch($this->defaults, strlen($this->route));
                }
            }

            return null;
        }

        if ($path === $this->route) {
            return new RouteMatch($this->defaults, strlen($this->route));
        }

        return null;
    }

    /**
     * @param EventInterface $event
     * @return null|RouteMatch
     */
    public function __invoke(EventInterface $event)
    {
        return $this->match($event->uri(), $event->pathOffset());
    }
}
