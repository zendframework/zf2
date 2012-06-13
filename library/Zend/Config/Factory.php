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

use Zend\Stdlib\ArrayUtils;
use Zend\Loader\Broker;

/**
 * Declared abstract to prevent instantiation
 * 
 * @category  Zend
 * @package   Zend_Config
 */
abstract class Factory
{
    /**
     * Readers used for config files.
     * array key is extension, array value is reader plugin name
     * @see Zend\Config\ReaderLoader::$plugins
     *
     * @var array
     */
    protected static $readers = array(
        'ini'  => 'ini',
        'xml'  => 'xml',
        'json' => 'json',
        'yaml' => 'yaml',
    );

    /**
     * @var Broker
     */
    protected static $readerBroker = null;

    /**
     * Read a config from a file.
     *
     * @param  string  $filename
     * @param  boolean $returnConfigObject 
     * @return array|Config
     * @throws Exception\RuntimeException
     */
    public static function fromFile($filename, $returnConfigObject = false)
    {
        $pathinfo = pathinfo($filename);
        
        if (!isset($pathinfo['extension'])) {
            throw new Exception\RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }
        
        $extension = strtolower($pathinfo['extension']);
       
        if ($extension === 'php') {
            if (!is_readable($filename)) {
                throw new Exception\RuntimeException(sprintf(
                    "File '%s' doesn't exist or not readable",
                    $filename
                ));
            }
            
            $config = include $filename;
        } elseif (isset(self::$readers[$extension])) {
            $plugin = self::$readers[$extension];
            if (!$plugin instanceof Reader\ReaderInterface) {
                $plugin = static::getReaderBroker()->load($plugin);
                self::$readers[$extension] = $plugin;
            }

            /** @var Reader\ReaderInterface $plugin  */
            $config = $plugin->fromFile($filename);
        } else {
            throw new Exception\RuntimeException(sprintf(
                'Unsupported config file extension: .%s',
                $pathinfo['extension']
            ));
        }

        return ($returnConfigObject) ? new Config($config) : $config;
    }

    /**
     * Read configuration from multiple files and merge them.
     *
     * @param  array   $files
     * @param  boolean $returnConfigObject 
     * @return array|Config
     */
    public static function fromFiles(array $files, $returnConfigObject = false)
    {
        $config = array();

        foreach ($files as $file) {
            $config = ArrayUtils::merge($config, self::fromFile($file));
        }

        return ($returnConfigObject) ? new Config($config) : $config;
    }

    /**
     * Set config reader for file extension
     *
     * @param string $extension
     * @param string|Reader\ReaderInterface $reader
     * @throws Exception\InvalidArgumentException
     */
    public static function registerExtension($extension, $reader)
    {
        $extension = strtolower($extension);

        if (!is_string($reader) && !$reader instanceof Reader\ReaderInterface) {
            throw new Exception\InvalidArgumentException(
                'Reader should be plugin name or instance of Zend\Config\Reader\ReaderInterface'
            );
        }

        self::$readers[$extension] = $reader;
    }

    /**
     * Get config reader broker
     *
     * @return Broker
     */
    public static function getReaderBroker()
    {
        if (self::$readerBroker === null) {
            self::$readerBroker = new ReaderBroker();
        }
        return self::$readerBroker;
    }

    /**
     * Change config reader broker
     *
     * @param  Broker $broker
     * @return void
     */
    public static function setReaderBroker(Broker $broker)
    {
        self::$readerBroker = $broker;
    }

    /**
     * Resets internal config reader broker
     *
     * @return void
     */
    public static function resetReaderBroker()
    {
        self::$readerBroker = null;
    }
}
