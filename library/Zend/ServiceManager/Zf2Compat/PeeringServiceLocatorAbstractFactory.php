<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Zf2Compat;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Peering service locator abstract factory - allows peering between service locators even though it's
 * not a first class citizien of the framework anymore
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class PeeringServiceLocatorAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $peerLocator;

    /**
     * @param ServiceLocatorInterface $peerLocator
     */
    public function __construct(ServiceLocatorInterface $peerLocator)
    {
        $this->peerLocator = $peerLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->peerLocator->has($requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->peerLocator->get($requestedName);
    }
}
