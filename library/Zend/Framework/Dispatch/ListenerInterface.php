<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Controller\Manager\ListenerInterface as ControllerManager;
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Event\Manager\ListenerInterface as EventManager;
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
     * @param ViewModel $vm
     * @return self
     */
    public function setViewModel(ViewModel $vm);

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @param $response
     * @return mixed
     */
    public function trigger(EventInterface $event, $response);
}
