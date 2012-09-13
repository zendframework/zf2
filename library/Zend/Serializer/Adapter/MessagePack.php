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

use Zend\Serializer\Exception;
use Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class MessagePack extends AbstractAdapter
{
    /**
     * @var string Serialized null value
     */
    private static $serializedNull = null;

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException If igbinary extension is not present
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('msgpack')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "msgpack" is required for this adapter'
            );
        }

        if (self::$serializedNull === null) {
            self::$serializedNull = msgpack_pack(null);
        }

        parent::__construct($options);
    }

    /**
     * Serialize PHP value to message pack
     *
     * @param  mixed $value
     * @return string
     * @throws Exception\RuntimeException on igbinary error
     */
    public function serialize($value)
    {
        ErrorHandler::start();
        $ret = msgpack_pack($value);
        $err = ErrorHandler::stop();

        if ($ret === false) {
            throw new Exception\RuntimeException('Serialization failed', 0, $err);
        }

        return $ret;
    }

    /**
     * Deserialize igbinary string to PHP value
     *
     * @param  string $serialized
     * @return mixed
     * @throws Exception\RuntimeException on igbinary error
     */
    public function unserialize($serialized)
    {
        if ($serialized === self::$serializedNull) {
            return null;
        }

        ErrorHandler::start();
        $ret = msgpack_unpack($serialized);
        $err = ErrorHandler::stop();

        if ($ret === null) {
            throw new Exception\RuntimeException('Unserialization failed', 0, $err);
        }

        return $ret;
    }
}
