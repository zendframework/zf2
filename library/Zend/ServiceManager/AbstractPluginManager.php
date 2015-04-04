<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\Exception\InvalidServiceException;

/**
 * Abstract plugin manager
 */
abstract class AbstractPluginManager extends ServiceManager implements PluginManagerInterface
{
    /**
     * An object type that the created instance must be instanced of
     *
     * @var string
     */
    protected $instanceOf = null;

    /**
     * @param ServiceLocatorInterface $parentLocator
     * @param array                   $config
     */
    public function __construct(ServiceLocatorInterface $parentLocator, array $config)
    {
        parent::__construct($config);
        $this->creationContext = $parentLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name, array $options = [])
    {
        $instance = parent::get($name, $options);
        $this->validate($instance);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($instance)
    {
        if (empty($this->instanceOf) || $instance instanceof $this->instanceOf) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            __CLASS__,
            $this->instanceOf,
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }
}