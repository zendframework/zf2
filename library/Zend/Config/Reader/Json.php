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

use Zend\Config\Exception\RuntimeException;
use Zend\Json\Json as JsonFormat;
use Zend\Json\Exception as JsonException;

/**
 * JSON config reader.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Json implements ReaderInterface
{
    /**
     * Directory of the JSON file
     *
     * @var string
     */
    protected $directory;

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
            throw new Exception\RuntimeException(sprintf(
                "File '%s' doesn't exist or not readable",
                $filename
            ));
        }
        
        $this->directory = dirname($filename);
        
        try {
            $config = JsonFormat::decode(file_get_contents($filename), JsonFormat::TYPE_ARRAY);
        } catch (JsonException\RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
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
        if (empty($string)) {
            return array();
        }

        $this->directory = null;
        
        try {
            $config = JsonFormat::decode($string, JsonFormat::TYPE_ARRAY);
        } catch (JsonException\RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
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
                    throw new RuntimeException('Cannot process @include statement for a JSON string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            } 
        }
        return $data;
    }
}
