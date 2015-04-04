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
 * Interface for a plugin manager
 *
 * A plugin manager is a specialized service locator used to create homogeneous objects
 */
interface PluginManagerInterface extends ServiceLocatorInterface
{
    /**
     * Validate an instance
     *
     * @param  object $instance
     * @return void
     * @throws InvalidServiceException
     */
    public function validate($instance);
}