<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\Manager;

use Zend\Framework\Mvc\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Mvc\Service\ListenerFactoryInterface as ServiceManager;

class ListenerFactory
    implements FactoryInterface
{
    /**
     * Create Event Manager listener
     *
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return new Listener;
    }
}
