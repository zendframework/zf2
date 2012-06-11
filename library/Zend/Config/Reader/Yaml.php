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
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Reader;

use Zend\Config\Exception;

/**
 * Yaml config reader.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Yaml implements ReaderInterface
{
    /**
     * Directory of the YAML file
     * 
     * @var string
     */
    protected $directory;

    /**
     * Yaml decoder callback
     * 
     * @var callback
     */
    protected $yamlDecoder;

    /**
     * Constructor
     * 
     * @param callback $yamlDecoder
     */
    public function __construct($yamlDecoder = null)
    {
        if (!empty($yamlDecoder)) {
            $this->setYamlDecoder($yamlDecoder);
        } else {
            if (function_exists('yaml_parse')) {
                $this->setYamlDecoder('yaml_parse');
            }
        }
    }

    /**
     * Set callback for decoding YAML
     *
     * @param  string|\Closure $yamlDecoder the decoder to set
     * @return Yaml
     * @throws \Zend\Config\Exception\InvalidArgumentException
     */
    public function setYamlDecoder($yamlDecoder)
    {
        if (!is_callable($yamlDecoder)) {
            throw new Exception\InvalidArgumentException('Invalid parameter to setYamlDecoder() - must be callable');
        }
        $this->yamlDecoder = $yamlDecoder;
        return $this;
    }

    /**
     * Get callback for decoding YAML
     *
     * @return callback
     */
    public function getYamlDecoder()
    {
        return $this->yamlDecoder;
    }

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromFile()
     * @param  string $filename
     * @return array
     * @throws \Zend\Config\Exception\RuntimeException
     */
    public function fromFile($filename)
    {
        if (!is_readable($filename)) {
            throw new Exception\RuntimeException("File '{$filename}' doesn't exist or not readable");
        }

        if (null === $this->getYamlDecoder()) {
             throw new Exception\RuntimeException("You didn't specify a Yaml callback decoder");
        }
        
        $this->directory = dirname($filename);
        
        $config = call_user_func($this->getYamlDecoder(), file_get_contents($filename));
        if (null === $config) {
            throw new Exception\RuntimeException("Error parsing YAML data");
        }  
        
        return $this->process($config);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param string $string
     * @return array
     * @throws \Zend\Config\Exception\RuntimeException
     */
    public function fromString($string)
    {
        if (null === $this->getYamlDecoder()) {
             throw new Exception\RuntimeException("You didn't specify a Yaml callback decoder");
        }
        if (empty($string)) {
            return array();
        }
        
        $this->directory = null;
        
        $config = call_user_func($this->getYamlDecoder(), $string);
        if (null === $config) {
            throw new Exception\RuntimeException("Error parsing YAML data");
        }   
        
        return $this->process($config);
    }

    /**
     * Process the array for @include
     *
     * @param array $data
     * @return array
     * @throws \Zend\Config\Exception\RuntimeException
     */
    protected function process(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->process($value);
            }
            if (trim($key) === '@include') {
                if ($this->directory === null) {
                    throw new Exception\RuntimeException('Cannot process @include statement for a json string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            } 
        }
        return $data;
    }
}
