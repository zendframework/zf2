<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\ResponseSender;

use Zend\Mvc\ResponseSender\SendResponseEvent;;

interface ResponseSenderInterface
{
    /**
     * Send the response
     *
     * @param SendResponseEvent $event
     * @return void
     */
    public function __invoke(SendResponseEvent $event);
}
