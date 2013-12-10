<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

/**
 * @method \Zend\Mvc\Controller\PluginManager get($name)
 */
class ControllerPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\Mvc\Controller\PluginManager';
    const PLUGIN_CONFIG_CLASS = 'Zend\Mvc\Controller\PluginManagerConfig';
}
