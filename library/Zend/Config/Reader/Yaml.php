<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace Zend\Config\Reader;

use Zend\Config\Exception\InvalidArgumentException;
use Zend\Config\Exception\RuntimeException;

/**
 * YAML config reader.
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
     * YAML decoder callback
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
        if ($yamlDecoder !== null) {
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
     * @param  string|callback|\Closure $yamlDecoder the decoder to set
     * @return Yaml
     * @throws InvalidArgumentException
     */
    public function setYamlDecoder($yamlDecoder)
    {
        if (!is_callable($yamlDecoder)) {
            throw new InvalidArgumentException('Invalid parameter to setYamlDecoder() - must be callable');
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
     * @throws RuntimeException
     */
    public function fromFile($filename)
    {
        if (!is_readable($filename)) {
            throw new Exception\RuntimeException(sprintf(
                "File '%s' doesn't exist or not readable",
                $filename
            ));
        }

        if (null === $this->getYamlDecoder()) {
             throw new RuntimeException("You didn't specify a Yaml callback decoder");
        }
        
        $this->directory = dirname($filename);
        
        $config = call_user_func($this->getYamlDecoder(), file_get_contents($filename));
        if (null === $config) {
            throw new RuntimeException("Error parsing YAML data");
        }  
        
        return $this->process($config);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param  string $string
     * @return array|bool
     * @throws RuntimeException
     */
    public function fromString($string)
    {
        if (null === $this->getYamlDecoder()) {
             throw new RuntimeException("You didn't specify a Yaml callback decoder");
        }
        if (empty($string)) {
            return array();
        }
        
        $this->directory = null;
        
        $config = call_user_func($this->getYamlDecoder(), $string);
        if (null === $config) {
            throw new RuntimeException("Error parsing YAML data");
        }   
        
        return $this->process($config);
    }

    /**
     * Process the array for @include
     *
     * @param  array $data
     * @return array
     * @throws RuntimeException
     */
    protected function process(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->process($value);
            }
            if (trim($key) === '@include') {
                if ($this->directory === null) {
                    throw new RuntimeException('Cannot process @include statement for a json string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            } 
        }
        return $data;
    }
}
