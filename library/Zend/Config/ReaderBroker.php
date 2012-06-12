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

use Zend\Loader\PluginBroker;
use Zend\Config\Reader\ReaderInterface;

/**
 * Broker for serializer adapter instances
 *
 * @category   Zend
 * @package    Zend_Serializer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReaderBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Config\ReaderLoader';

    /**
     * Determine if we have a valid reader
     * 
     * @param  mixed $plugin
     * @return bool
     * @throws Exception\RuntimeException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof ReaderInterface) {
            throw new Exception\RuntimeException(
                'Config readers must implement Zend\Config\Reader\ReaderInterface'
            );
        }
        return true;
    }
}
