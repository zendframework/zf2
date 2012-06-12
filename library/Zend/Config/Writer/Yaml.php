<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace Zend\Config\Writer;

use Zend\Config\Exception\InvalidArgumentException;
use Zend\Config\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Yaml extends AbstractWriter
{
    /**
     * YAML encoder callback
     * 
     * @var callback
     */
    protected $yamlEncoder;

    /**
     * Constructor
     * 
     * @param callback|string|null $yamlEncoder
     */
    public function __construct($yamlEncoder = null)
    {
        if ($yamlEncoder !== null) {
            $this->setYamlEncoder($yamlEncoder);
        } else {
            if (function_exists('yaml_emit')) {
                $this->setYamlEncoder('yaml_emit');
            }
        }
    }

    /**
     * Get callback for decoding YAML
     *
     * @return callback
     */
    public function getYamlEncoder()
    {
        return $this->yamlEncoder;
    }

    /**
     * Set callback for decoding YAML
     *
     * @param  callback $yamlEncoder the decoder to set
     * @return Yaml
     * @throws InvalidArgumentException
     */
    public function setYamlEncoder($yamlEncoder)
    {
        if (!is_callable($yamlEncoder)) {
            throw new InvalidArgumentException('Invalid parameter to setYamlEncoder() - must be callable');
        }
        $this->yamlEncoder = $yamlEncoder;
        return $this;
    }

    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     * @throws RuntimeException
     */
    public function processConfig(array $config)
    {
        if (null === $this->getYamlEncoder()) {
             throw new RuntimeException("You didn't specify a Yaml callback encoder");
        }
        
        $config = call_user_func($this->getYamlEncoder(), $config);
        if (null === $config) {
            throw new RuntimeException("Error generating YAML data");
        }
        
        return $config;
    }
}
