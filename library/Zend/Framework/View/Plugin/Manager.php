<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Framework\ServiceManager\AbstractPluginManager;
use Zend\Framework\ServiceManager\ConfigInterface;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\View\Plugin\Request as PluginRequest;

use Zend\View\Renderer\RendererInterface as Renderer;

use Zend\Framework\View\Plugin\ManagerInterface;

/**
 * Plugin manager implementation for view helpers
 *
 * Enforces that helpers retrieved are instances of
 * Helper\HelperInterface. Additionally, it registers a number of default
 * helpers.
 */
class Manager extends AbstractPluginManager implements ManagerInterface
{
    /**
     * Validate the plugin
     *
     * Checks that the helper loaded is an instance of Helper\HelperInterface.
     *
     * @param  mixed                            $plugin
     * @return void
     * @throws Exception\InvalidHelperException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Helper\HelperInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidHelperException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Helper\HelperInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
