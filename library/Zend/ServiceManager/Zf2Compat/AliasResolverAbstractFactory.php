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
use Zend\ServiceManager\Exception;

/**
 * Alias abstract factory - allows usage of aliases in {@see \Zend\ServiceManager\ServiceManager} even
 * though they are no more a first-class citizens of the component.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class AliasResolverAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceManager;

    /**
     * @var array
     */
    private $aliases = [];

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
        return $this->hasAlias($requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (! $this->hasAlias($requestedName)) {
            throw new Exception\ServiceNotFoundException(sprintf(
                'Could not find service "%s" in the configured aliases'
            ));
        }

        return $serviceLocator->get($this->aliases[$requestedName]);
    }

    /**
     * @param  string $alias
     * @param  string $nameOrAlias
     *
     * @return void
     *
     * @throws Exception\ServiceNotFoundException
     * @throws Exception\InvalidServiceNameException
     */
    public function setAlias($alias, $nameOrAlias)
    {
        if (!is_string($alias) || !is_string($nameOrAlias)) {
            throw new Exception\InvalidServiceNameException('Service or alias names must be strings.');
        }

        if ($alias == '' || $nameOrAlias == '') {
            throw new Exception\InvalidServiceNameException('Invalid service name alias');
        }

        if ((! $this->serviceManager->getAllowOverride()) &&  $this->hasAlias($alias)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'An alias by the name "%s" already exists',
                $alias
            ));
        }

        /*if ((! $this->serviceManager->getAllowOverride()) && $this->serviceManager->has($alias, false)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'An alias by the name "%s" already exists',
                $alias
            ));
        }*/

        if ($this->serviceManager->hasAlias($alias)) {
            $this->checkForCircularAliasReference($alias, $nameOrAlias);
        }

        $this->aliases[$alias] = $nameOrAlias;
    }

    /**
     * Remove an alias from the registered ones
     *
     * @param  string $alias
     * @return bool
     */
    public function removeAlias($alias)
    {
        if ($this->hasAlias($alias)) {
            unset($this->aliases[$alias]);

            return true;
        }

        return false;
    }

    /**
     * Determine if we have an alias
     *
     * @param  string $alias
     * @return bool
     */
    public function hasAlias($alias)
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * Retrieves a map of all the currently set aliases, with keys being the
     * alias name, and values being the real name of the service
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Ensure the alias definition will not result in a circular reference
     *
     * @param  string $alias
     * @param  string $nameOrAlias
     *
     * @return void
     *
     * @throws Exception\CircularReferenceException
     */
    protected function checkForCircularAliasReference($alias, $nameOrAlias)
    {
        $aliases         = $this->aliases;
        $aliases[$alias] = $nameOrAlias;
        $stack           = array();

        while (isset($aliases[$alias])) {
            if (isset($stack[$alias])) {
                throw new Exception\CircularReferenceException(sprintf(
                    'The alias definition "%s" : "%s" results in a circular reference: "%s" -> "%s"',
                    $alias,
                    $nameOrAlias,
                    implode('" -> "', $stack),
                    $alias
                ));
            }

            $stack[$alias] = $alias;
            $alias         = $aliases[$alias];
        }
    }
}
