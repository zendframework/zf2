<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\EventManager\Event as EventManagerEvent;
use Zend\Framework\EventManager\ListenerInterface as EventListener;

class Event extends EventManagerEvent
{
    protected $name = 'sendResponse';

    protected $response;

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }


    public function __invoke(EventListener $listener)
    {
        $response = $listener($this);

        $this->eventResponses[] = $response;

        if ($this->callback) {
            call_user_func($this->callback, $this, $listener, $response);
        }
    }
}
