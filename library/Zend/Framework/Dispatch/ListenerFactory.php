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
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\View\ServicesTrait as View;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use Controller,
        EventManager,
        View;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function service(Request $request, array $options = [])
    {
        return (new Listener)->setEventManager($this->eventManager())
                             ->setViewModel($this->viewModel())
                             ->setControllerManager($this->controllerManager());
    }
}
