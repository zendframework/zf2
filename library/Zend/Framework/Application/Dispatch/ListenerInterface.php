<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Controller\Manager\ListenerInterface as ControllerManager;
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Event\Manager\ListenerInterface as EventManager;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ModelInterface as ViewModel;

interface ListenerInterface
    extends Listener
{
    /**
     * @param ControllerManager $cm
     * @return self
     */
    public function setControllerManager(ControllerManager $cm);

    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @return bool|RouteMatch
     */
    public function routeMatch();

    /**
     * @param ViewModel $vm
     * @return self
     */
    public function setViewModel(ViewModel $vm);

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event);
}
