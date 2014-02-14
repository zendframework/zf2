<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\ManagerTrait as ServiceManager;
use Zend\Framework\Service\RequestInterface as Request;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use EventManager,
        ServiceManager;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function __invoke(Request $request, array $options = [])
    {
        return (new Listener)->setEventManager($this->eventManager())
                             ->setServiceManager($this->serviceManager());
    }
}
