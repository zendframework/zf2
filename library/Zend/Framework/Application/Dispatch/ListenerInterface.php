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
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Service\EventManager\ListenerInterface as EventManager;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\View\Model\ModelInterface as ViewModel;

interface ListenerInterface
    extends Listener
{
    /**
     * @param EventManager $em
     * @return self
     */
    public function setEventManager(EventManager $em);

    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);

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
