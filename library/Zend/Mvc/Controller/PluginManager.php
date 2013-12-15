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
use Zend\Framework\ServiceManager\AbstractPluginManager;
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
     * Default set of plugins factories
     *
     * @var array
     */
    protected $factories = array(
        'forward'  => 'Zend\Mvc\Controller\Plugin\Service\ForwardFactory',
        'identity' => 'Zend\Mvc\Controller\Plugin\Service\IdentityFactory',
    );

    /**
     * Default set of plugins
     *
     * @var array
     */
    protected $invokableClasses = array(
        'acceptableviewmodelselector' => 'Zend\Mvc\Controller\Plugin\AcceptableViewModelSelector',
        'filepostredirectget'         => 'Zend\Mvc\Controller\Plugin\FilePostRedirectGet',
        'flashmessenger'              => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
        'layout'                      => 'Zend\Mvc\Controller\Plugin\Layout',
        'params'                      => 'Zend\Mvc\Controller\Plugin\Params',
        'postredirectget'             => 'Zend\Mvc\Controller\Plugin\PostRedirectGet',
        'redirect'                    => 'Zend\Mvc\Controller\Plugin\Redirect',
        'url'                         => 'Zend\Mvc\Controller\Plugin\Url',
    );

    /**
     * Default set of plugin aliases
     *
     * @var array
     */
    protected $aliases = array(
        'prg'     => 'postredirectget',
        'fileprg' => 'filepostredirectget',
    );

    /**
     * @var DispatchableInterface
     */
    protected $controller;

    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function get($name, $options = [])
    {
        $plugin = parent::get($name, $options);
        $plugin->setController($this->controller);

        return $plugin;
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
}
