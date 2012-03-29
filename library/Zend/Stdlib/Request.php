<?php

namespace Zend\Stdlib;

/**
 * Generic request implementation
 */
class Request extends Message {

    /**
     * @var ParametersDescription
     */
    protected $routeMatch;

    /**
     * Set the RouteMatch instance that corresponds to this request.
     *
     * @param \Zend\Stdlib\ParametersDescription $routeMatch
     * @return Request
     */
    public function setRouteMatch(ParametersDescription $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * Return the RouteMatch instance holding matched route parameters.
     *
     * @return \Zend\Stdlib\ParametersDescription
     */
    public function routeMatch()
    {
        if ($this->routeMatch === null) {
            $this->routeMatch = new Parameters();
        }

        return $this->routeMatch;
    }

}
