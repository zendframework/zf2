<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Route;

use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Request\ServicesTrait as RequestService;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use EventManager;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function service(Request $request, array $options = [])
    {
        return (new Listener)->setServiceManager($this->sm)
                             ->setEventManager($this->eventManager());
    }
}
