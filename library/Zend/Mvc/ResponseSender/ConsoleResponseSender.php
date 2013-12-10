<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\ResponseSender;

use Zend\Console\Response;
use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\Listener as EventListener;

class ConsoleResponseSender extends EventListener
{
    /**
     * Send content
     *
     * @param  EventInterface $event
     * @return ConsoleResponseSender
     */
    public function sendContent(EventInterface $event)
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
     * Send the response
     *
     * @param  EventInterface $event
     */
    public function __invoke(EventInterface $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        $this->sendContent($event);
        $errorLevel = (int) $response->getMetadata('errorLevel',0);
        $event->stopPropagation(true);
        exit($errorLevel);
    }
}
