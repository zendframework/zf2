<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

trait SendContentTrait
{
    /**
     * Send content
     *
     * @param  EventInterface $event
     * @return self
     */
    public function sendContent(EventInterface $event)
    {
        if ($event->contentSent()) {
            return $this;
        }

        $response = $event->target();

        echo $response->getContent();

        $event->setContentSent();

        return $this;
    }
}
