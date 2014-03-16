<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Route\Match\ServiceTrait as RouteMatchTrait;
use Zend\Mvc\Router\RouteMatch as RouteMatch;
use Zend\Uri\Http as Uri;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        RouteMatchTrait;

    /**
     * @param Uri $uri
     * @param int|null $pathLength
     * @param int|null $pathOffset
     */
    public function __construct(Uri $uri, $pathLength, $pathOffset)
    {
        $this->pathLength    = $pathLength;
        $this->pathOffset    = $pathOffset;
        $this->uri           = $uri;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        $response = $listener($this, $options);

        if (!$response instanceof RouteMatch) {
            return $response;
        }

        $this->setRouteMatch($response);

        if ($this->pathLength === null || $response->getLength() === $this->pathLength) {

            $response->setMatchedRouteName($listener->name());

            $this->stop();
        }

        return $response;
    }
}
