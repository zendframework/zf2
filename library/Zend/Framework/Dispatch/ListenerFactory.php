<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Controller\ServicesTrait as Controller;
use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\Framework\View\ServicesTrait as View;

class ListenerFactory
    extends FactoryListener
{
    /**
     *
     */
    use Controller,
        View,
        EventManager;

    /**
     * @param EventInterface $event
     * @return Listener
     */
    public function service(EventInterface $event)
    {
        $listener = new Listener;

        $listener->setEventManager($this->eventManager())
                 ->setViewModel($this->viewModel())
                 ->setControllerManager($this->controllerManager());

        return $listener;
    }
}
