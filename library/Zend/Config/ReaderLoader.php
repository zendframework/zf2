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

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for config readers.
 *
 * @category   Zend
 * @package    Zend_Serializer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReaderLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased config readers
     */
    protected $plugins = array(
        'ini'  => 'Zend\Config\Reader\Ini',
        'json' => 'Zend\Config\Reader\Json',
        'xml'  => 'Zend\Config\Reader\Xml',
        'yaml' => 'Zend\Config\Reader\Yaml',
    );
}
