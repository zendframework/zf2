<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Dispatch;

use Zend\Framework\Controller\Dispatch\ListenerInterface as Controller;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Model\ViewModel;

interface EventInterface
    extends Event
{
    /**
     * @return mixed
     */
    public function getError();

    /**
     * @param $error
     * @return self
     */
    public function setError($error);

    /**
     * @param ServiceManager $sm
     * @return mixed
     */
    public function setServiceManager(ServiceManager $sm);

    /**
     * @return Controller
     */
    public function getController();

    /**
     * @param Controller $controller
     * @return self
     */
    public function setController(Controller $controller);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     * @return self
     */
    public function setResult($result);

    /**
     * @return ViewModel
     */
    public function getViewModel();
    /**
     * @param ViewModel $vm
     * @return mixed
     */
    public function setViewModel(ViewModel $vm);
}
