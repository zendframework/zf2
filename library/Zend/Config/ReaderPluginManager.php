<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace Zend\Config;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for config readers
 *
 * Enforces that readers retrieved are instances of Reader\ReaderInterface.
 * Additionally, it registers a number of default readers available.
 *
 * @category   Zend
 * @package    Zend_Config
 */
class ReaderPluginManager extends AbstractPluginManager
{
    /**
     * Default set of adapters
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'ini'  => 'Zend\Config\Reader\Ini',
        'json' => 'Zend\Config\Reader\Json',
        'xml'  => 'Zend\Config\Reader\Xml',
        'yaml' => 'Zend\Config\Reader\Yaml',
    );

    /**
     * Don't share by default
     * 
     * @var array
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the pattern adapter loaded is an instance of Pattern\PatternInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Reader\ReaderInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Reader\ReaderInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
