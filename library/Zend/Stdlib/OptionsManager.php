<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * Class OptionsManager
 */
class OptionsManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $abstractFactories = array(
        'Zend\ServiceManager\OptionsClassFactory'
    );

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if (! $plugin instanceof AbstractOptions) {
            throw new Exception\RuntimeException(sprintf(
                'Options of type %s is invalid; must extend Zend\Stdlib\AbstractOptions',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin))
            ));
        }
    }
}
