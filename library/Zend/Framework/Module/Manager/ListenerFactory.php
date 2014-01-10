<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Manager;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\ModuleManager\ModuleManager;

class ListenerFactory
    extends FactoryListener
{
    /**
     *
     */
    use Config,
        EventManager;

    /**
     * @param EventInterface $event
     * @return void|ModuleManager
     */
    public function __invoke(EventInterface $event)
    {
        $modules = $this->applicationConfig()['modules'];

        $em = $this->eventManager();

        $em->add($this->sm->service('ModuleManager\DefaultListeners'));

        $mm = new ModuleManager($modules);

        $mm->setEventManager($em);

        //$mm->setSharedEventManager($em);

        return $mm;
    }
}
