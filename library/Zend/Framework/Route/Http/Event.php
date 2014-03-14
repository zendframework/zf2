<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\Route\ServiceTrait as Route;
use Zend\Mvc\Router\RouteMatch as RouteMatch;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        Route;

    /**
     * @param $request
     * @param $baseUrlLength
     * @param $pathLength
     * @param $options
     */
    public function __construct($request, $baseUrlLength, $pathLength, $options)
    {
        $this->request       = $request;
        $this->baseUrlLength = $baseUrlLength;
        $this->pathLength    = $pathLength;
        $this->options       = $options;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        $response = $listener($this, $options);

        if ($response instanceof RouteMatch) {

            $this->setRouteMatch($response);

            if ($this->pathLength === null || $response->getLength() === $this->pathLength) {

                //$response->setMatchedRouteName($name);

                //foreach ($this->defaultParams as $paramName => $value) {
                    //if ($response->getParam($paramName) === null) {
                        //$response->setParam($paramName, $value);
                    //}
                //}

                $this->stop();
            }
        }

        return $response;
    }
}
