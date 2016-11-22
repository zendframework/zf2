<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Manager;

use Zend\Stdlib\ResponseInterface;

trait ServiceTrait
{

    /**
     * Route manager
     *
     * @var ManagerInterface
     */
    protected $rm;

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function response(ResponseInterface $response)
    {
        return $this->rm->response($response);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function send(ResponseInterface $response)
    {
        return $this->rm->send($response);
    }

    /**
     * Set the route manager.
     *
     * @param  ManagerInterface $rm
     * @return self
     */
    public function setResponseManager(ManagerInterface $rm)
    {
        $this->rm = $rm;
        return $this;
    }

    /**
     * Get the route manager.
     *
     * @return ManagerInterface
     */
    public function responseManager()
    {
        return $this->rm;
    }
}
