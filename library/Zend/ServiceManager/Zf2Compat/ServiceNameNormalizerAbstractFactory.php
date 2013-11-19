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
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Alias abstract factory - allows usage of normalized service names in {@see \Zend\ServiceManager\ServiceManager}
 * even though they are no more a first-class citizens of the component.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceNameNormalizerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Lookup map for canonicalized names.
     *
     * @var string[]
     */
    protected $canonicalNames = [];

    /**
     * Lookup map for real service names
     *
     * @var string[]
     */
    protected $realServiceNames = [];

    /**
     * @var array map of characters to be replaced through strtr
     */
    protected $canonicalNamesReplacements = array('-' => '', '_' => '', ' ' => '', '\\' => '', '/' => '');

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator->has($this->getCanonicalNameMatch($name), true, false);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator->get($this->getCanonicalNameMatch($name), true, false);
    }

    /**
     * Find a matching service name
     *
     * @param $name
     * @return string
     */
    public function getCanonicalNameMatch($name)
    {
        if (isset($this->realServiceNames[$name])) {
            return $this->realServiceNames[$name];
        }

        $canonicalName = $this->canonicalizeName($name);

        if (isset($this->realServiceNames[$canonicalName])) {
            return $this->realServiceNames[$canonicalName];
        }

        // Find a service whose name (once canonicalized) corresponds fits
        foreach ($this->serviceManager->getRegisteredServices() as $serviceType) {
            foreach ($serviceType as $realServiceName) {
                $serviceCanonicalName = $this->canonicalizeName($realServiceName);

                if ($serviceCanonicalName === $canonicalName) {
                    $this->canonicalNames[$canonicalName] = $realServiceName;
                    $this->canonicalNames[$name]          = $realServiceName;

                    return $realServiceName;
                }
            }
        }

        return $canonicalName;
    }

    /**
     * Canonicalize name
     *
     * @param  string $name
     * @return string
     */
    protected function canonicalizeName($name)
    {
        if (isset($this->canonicalNames[$name])) {
            return $this->canonicalNames[$name];
        }

        // this is just for performance instead of using str_replace
        return $this->canonicalNames[$name] = strtolower(strtr($name, $this->canonicalNamesReplacements));
    }
}
