<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\EventManager\Listener as EventListener;

use Zend\Framework\MvcEvent;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\CreateServiceTrait as CreateService;

abstract class AbstractController
    extends EventListener
{
    /**
     * @var string
     */
    protected $eventName = MvcEvent::EVENT_CONTROLLER_DISPATCH;

    /**
     * @param ServiceManager $sm
     * @return mixed|static
     */
    public function createService(ServiceManager $sm)
    {
        $listener = new static();

        $listener->setPluginManager($sm->getControllerPluginManager());

        return $listener;
    }
}
