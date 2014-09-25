<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper\HeadMeta;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\View\Exception;

/**
 * Manager for HeadMeta plugin implementations.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class PluginManager extends AbstractPluginManager
{
    protected $invokableClasses = array(
        'opengraph' => 'Zend\View\Helper\HeadMeta\Plugin\OpenGraph',
        'mock'      => 'Zend\View\Helper\HeadMeta\Plugin\Mock',
    );

    /**
     * Validate the plugin.
     *
     * @param  mixed $plugin
     * @return void
     * @throws \Zend\View\Exception\InvalidArgumentException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Plugin\PluginInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'HeadMeta plugin of type %s is invalid; must implement %s\Plugin\PluginInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
