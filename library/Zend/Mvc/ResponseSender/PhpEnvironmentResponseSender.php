<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\ResponseSender;

use Zend\Http\PhpEnvironment\Response;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\ServiceManager\FactoryInterface;

class PhpEnvironmentResponseSender
    extends HttpResponseSender
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_RESPONSE;

    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    /**
     * Send php environment response
     *
     * @param  EventInterface $event
     * @return PhpEnvironmentResponseSender
     */
    public function __invoke(Event $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return $this;
        }

        $this->sendHeaders($event)
             ->sendContent($event);
        $event->stopPropagation(true);
        return $this;
    }
}
