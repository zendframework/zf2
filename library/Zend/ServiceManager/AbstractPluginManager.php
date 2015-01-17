<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

/**
 * Abstract plugin manager
 */
abstract class AbstractPluginManager extends ServiceManager implements PluginManagerInterface
{
    /**
     * @param ServiceLocatorInterface $parentLocator
     * @param array                   $config
     */
    public function __construct(ServiceLocatorInterface $parentLocator, array $config = [])
    {
        parent::__construct($config);
        $this->creationContext = $parentLocator;
    }
}