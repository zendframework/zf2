<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

trait ServiceLocatorAwareTrait
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * @var bool
     */
    protected $useTopServiceLocator = false;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $this->useTopServiceLocator
                ? $this->getTopServiceLocator($serviceLocator)
                : $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Get top service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ServiceLocatorInterface
     */
    protected function getTopServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        while ($serviceLocator instanceof ServiceLocatorAwareInterface && ($parentLocator = $serviceLocator->getServiceLocator()) != null) {
            $serviceLocator = $parentLocator;
        }
        return $serviceLocator;
    }
}
