<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\Controller\DispatchListenerInterface as Controller;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Model\ViewModel;

interface DispatchEventInterface
    extends Event
{
    /**
     *
     */
    const EVENT_CONTROLLER_DISPATCH = 'mvc.controller.dispatch';

    /**
     * @param ServiceManager $sm
     * @return mixed
     */
    public function setServiceManager(ServiceManager $sm);

    /**
     * @param DispatchListenerInterface $controller
     * @return mixed
     */
    public function setController(Controller $controller);

    /**
     * @param ViewModel $vm
     * @return mixed
     */
    public function setViewModel(ViewModel $vm);
}
