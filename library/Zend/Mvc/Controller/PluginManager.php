<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Mvc\Exception;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\Stdlib\DispatchableInterface;

/**
 * Plugin manager implementation for controllers
 *
 * Registers a number of default plugins, and contains an initializer for
 * injecting plugins with the current controller.
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * @var DispatchableInterface
     */
    protected $controller;

    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration ?: new PluginManagerConfig());

        $this->addInitializer([$this, 'injectController'], false);
    }

    /**
     * Set controller
     *
     * @param  DispatchableInterface $controller
     * @return PluginManager
     */
    public function setController(DispatchableInterface $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Retrieve controller instance
     *
     * @return null|DispatchableInterface
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Inject a helper instance with the registered controller
     *
     * @param  object $plugin
     * @return void
     */
    public function injectController($plugin)
    {
        if (!is_object($plugin)) {
            return;
        }
        if (!method_exists($plugin, 'setController')) {
            return;
        }

        $controller = $this->getController();
        if (!$controller instanceof DispatchableInterface) {
            return;
        }

        $plugin->setController($controller);
    }

    /**
     * Validate the plugin
     *
     * Any plugin is considered valid in this context.
     *
     * @param  mixed                            $plugin
     * @return void
     * @throws Exception\InvalidPluginException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Plugin\PluginInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidPluginException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Plugin\PluginInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
