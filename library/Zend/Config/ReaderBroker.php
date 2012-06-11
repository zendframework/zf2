<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
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
