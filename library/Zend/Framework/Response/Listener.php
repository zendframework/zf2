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
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Listener as ParentListener;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Http\PhpEnvironment\Response;

class Listener
    extends ParentListener
    implements FactoryInterface
{
    /**
     * @var string
     */
    protected $eventName = MvcEvent::EVENT_RESPONSE;

    /**
     * @param ServiceManager $sm
     * @return mixed|Listener
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * Send content
     *
     * @param  Event $event
     * @return $this
     */
    public function sendContent(Event $event)
    {
        if ($event->contentSent()) {
            return $this;
        }
        $response = $event->getResponse();
        echo $response->getContent();
        $event->setContentSent();
        return $this;
    }

    /**
     * @param Event $event
     * @return mixed|void
     */
    public function __invoke(Event $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        $this->sendContent($event);
        $errorLevel = (int) $response->getMetadata('errorLevel',0);
        $event->stopEventPropagation();
        exit($errorLevel);
    }
}
