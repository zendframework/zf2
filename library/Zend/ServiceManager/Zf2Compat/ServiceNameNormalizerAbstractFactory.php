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

/**
 * Alias abstract factory - allows usage of normalized service names in {@see \Zend\ServiceManager\ServiceManager}
 * even though they are no more a first-class citizens of the component.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceNameNormalizerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Lookup for canonicalized names.
     *
     * @var array
     */
    protected $canonicalNames = array();

    /**
     * @var array map of characters to be replaced through strtr
     */
    protected $canonicalNamesReplacements = array('-' => '', '_' => '', ' ' => '', '\\' => '', '/' => '');

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator->has($this->canonicalizeName($requestedName));
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator->get($this->canonicalizeName($requestedName));
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
