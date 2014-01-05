<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Stdlib\ResponseInterface as Response;

interface EventInterface
    extends Event
{
    /**
     * @return bool|object
     */
    public function response();

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response);

    /**
     * @return mixed
     */
    public function result();

    /**
     * @param $result
     * @return self
     */
    public function setResult($result);
}
