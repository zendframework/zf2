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
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Http\Response;

class HttpListener
    extends AbstractListener
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * Send content
     *
     * @param  Event $event
     * @return HttpResponseSender
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
     * Send HTTP response
     *
     * @param  Event $event
     * @return HttpResponseSender
     */
    public function __invoke(Event $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return $this;
        }

        $this->sendHeaders($event)
             ->sendContent($event);

        $event->stopEventPropagation();

        return $this;
    }
}
