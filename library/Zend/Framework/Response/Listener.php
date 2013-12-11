<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\EventManager\EventManager;
use Zend\Framework\EventManager\EventManagerInterface;
use Zend\Framework\EventManager\EventManagerAwareInterface;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\MvcEvent;
use Zend\Mvc\ResponseSender\ConsoleResponseSender;
use Zend\Mvc\ResponseSender\HttpResponseSender;
use Zend\Mvc\ResponseSender\PhpEnvironmentResponseSender;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\Mvc\ResponseSender\SimpleStreamResponseSender;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Framework\ServiceManager\ServiceManager;
use Zend\Framework\EventManager\CallbackListener;

class Listener implements
    ListenerAggregateInterface
{

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $em)
    {
        $this->listeners[] = $em->attach(new PhpEnvironmentResponseSender(MvcEvent::EVENT_RESPONSE, null, -10000));
        $this->listeners[] = $em->attach(new ConsoleResponseSender(MvcEvent::EVENT_RESPONSE, null, -20000));
        $this->listeners[] = $em->attach(new SimpleStreamResponseSender(MvcEvent::EVENT_RESPONSE, null, -30000));
        $this->listeners[] = $em->attach(new HttpResponseSender(MvcEvent::EVENT_RESPONSE, null, -40000));
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $em)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($em->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}
