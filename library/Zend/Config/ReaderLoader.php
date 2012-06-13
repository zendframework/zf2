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

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for config readers.
 *
 * @category   Zend
 * @package    Zend_Config
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
