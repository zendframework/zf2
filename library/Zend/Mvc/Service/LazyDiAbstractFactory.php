<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class LazyDiAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var DiAbstractServiceFactory
     */
    protected $wrappedDiAbstractFactory;

    /**
     * Initializes the wrapped $wrappedDiAbstractFactory if not already set
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function initialize(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->wrappedDiAbstractFactory) {
            /* @var $di \Zend\Di\Di */
            $di = $serviceLocator->get('Di');
            $this->wrappedDiAbstractFactory = new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $this->initialize($serviceLocator);
        return $this->wrappedDiAbstractFactory->canCreateServiceWithName($serviceLocator, $name, $requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $this->initialize($serviceLocator);
        return $this->wrappedDiAbstractFactory->createServiceWithName($serviceLocator, $name, $requestedName);
    }
}
