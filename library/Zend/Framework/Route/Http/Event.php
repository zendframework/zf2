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
     * @param int $pathOffset
     */
    public function __construct(Uri $uri, $pathOffset)
    {
        $this->uri        = $uri;
        $this->pathOffset = $pathOffset;
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

        if ($this->pathOffset === null || $response->getLength() === $this->pathOffset) {

            //$response->setMatchedRouteName($name);

            //foreach ($this->defaultParams as $paramName => $value) {
                //if ($response->getParam($paramName) === null) {
                    //$response->setParam($paramName, $value);
                //}
            //}

            //$this->stop();
        }

        $this->stop();

        return $response;
    }
}
