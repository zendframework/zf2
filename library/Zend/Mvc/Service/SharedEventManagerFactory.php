<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Service;

use Zend\EventManager\EventManager;
use Zend\EventManager\StaticEventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class SharedEventManagerFactory implements FactoryInterface
{
    /**
     * Create an SharedEventManager instance
     *
     * Creates a new EventManager instance, seeding it with a shared instance
     * of SharedEventManager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return EventManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return StaticEventManager::getInstance();
    }
}
