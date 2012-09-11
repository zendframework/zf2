<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace Zend\EventManager;

use Zend\EventManager\EventInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 */
interface EventProviderInterface
{
    /**
     * Compose an Event
     *
     * @param  Event $event
     * @return void
     */
    public function setEvent(EventInterface $event);

    /**
     * Retrieve the composed event
     *
     * @return Event
     */
    public function getEvent();
}
