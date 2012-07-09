<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\RuntimeException;
use Zend\Amf\Parser as AmfParser;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class Amf3 extends AbstractAdapter
{
    /**
     * Serialize a PHP value to AMF3 format
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws RuntimeException
     */
    public function serialize($value, array $opts = array())
    {
        try  {
            $stream     = new AmfParser\OutputStream();
            $serializer = new AmfParser\Amf3\Serializer($stream);
            $serializer->writeTypeMarker($value);
            return $stream->getStream();
        } catch (\Exception $e) {
            throw new RuntimeException('Serialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Deserialize an AMF3 value to PHP
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws RuntimeException
     */
    public function unserialize($value, array $opts = array())
    {
        try {
            $stream       = new AmfParser\InputStream($value);
            $deserializer = new AMFParser\Amf3\Deserializer($stream);
            return $deserializer->readTypeMarker();
        } catch (\Exception $e) {
            throw new RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
