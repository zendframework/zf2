<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Http\Response;
use Zend\Http\Request;
use Zend\Http\Header\Referer;
use Zend\Mvc\Exception;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;

/**
 * @todo       allow specifying status code as a default, or as an option to methods
 */
class Redirect extends AbstractPlugin
{
    /**
     * @var null|MvcEvent
     */
    protected $event;

    /**
     * @var null|Response
     */
    protected $response;

    /**
     * @var null|Request
     */
    protected $request;

    /**
     * Generates a URL based on a route
     *
     * @param  string                    $route              RouteInterface name
     * @param  array                     $params             Parameters to use in url generation, if any
     * @param  array                     $options            RouteInterface-specific options to use in url generation, if any
     * @param  bool                      $reuseMatchedParams Whether to reuse matched parameters
     * @return Response
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEventInterface, or
     *                                                      router cannot be found in controller event
     */
    public function toRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        $controller = $this->getController();
        if (!$controller || !method_exists($controller, 'plugin')) {
            throw new Exception\DomainException('Redirect plugin requires a controller that defines the plugin() method');
        }

        $urlPlugin = $controller->plugin('url');

        if (is_scalar($options)) {
            $url = $urlPlugin->fromRoute($route, $params, $options);
        } else {
            $url = $urlPlugin->fromRoute($route, $params, $options, $reuseMatchedParams);
        }

        return $this->toUrl($url);
    }

    /**
     * Redirect to referer URL if present in header otherwise redirect to the specified url
     * @param  string   $url URL to redirect if HTTP_REFERER is not present in request header
     * @return Response
     */
    public function toReferer($defaultUrl = null)
    {
        $url = $this->getRequest()->getHeader('Referer', $defaultUrl);

        if (!$url instanceof Referer) {
            if ($defaultUrl === null) {
                return $this->toRoute();
            }

            return $this->toUrl($defaultUrl);
        }

        return $this->toUrl($url->getUri());
    }

    /**
     * Redirect to the given URL
     *
     * @param  string   $url
     * @return Response
     */
    public function toUrl($url)
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }

    /**
     * Refresh to current route
     *
     * @return Response
     */
    public function refresh()
    {
        return $this->toRoute(null, array(), array(), true);
    }

    /**
     * Get the request
     *
     * @return Request
     * @throws Exception\DomainException if unable to find request
     */
    protected function getRequest()
    {
        if ($this->request) {
            return $this->request;
        }

        $event = $this->getEvent();
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            throw new Exception\DomainException('Redirect plugin requires event compose a request');
        }
        $this->request = $request;

        return $this->request;
    }

    /**
     * Get the response
     *
     * @return Response
     * @throws Exception\DomainException if unable to find response
     */
    protected function getResponse()
    {
        if ($this->response) {
            return $this->response;
        }

        $event    = $this->getEvent();
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            throw new Exception\DomainException('Redirect plugin requires event compose a response');
        }
        $this->response = $response;

        return $this->response;
    }

    /**
     * Get the event
     *
     * @return MvcEvent
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Redirect plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = $event->getParams();
            $event  = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }
}
