<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\RouteMatch;

use Zend\Framework\Controller\Manager\ServicesTrait as ControllerManager;
use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use ControllerManager;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function __invoke(Request $request, array $options = [])
    {
        return (new Listener)->setControllerManager($this->controllerManager());
    }
}
