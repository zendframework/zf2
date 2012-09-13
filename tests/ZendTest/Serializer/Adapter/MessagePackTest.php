<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace ZendTest\Serializer\Adapter;

use Zend\Serializer;
use Zend\Serializer\Exception\ExtensionNotLoadedException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @group      Zend_Serializer
 */
class MessagePackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer\Adapter\IgBinary
     */
    private $adapter;

    public function setUp()
    {
        if (!extension_loaded('msgpack')) {
            try {
                new Serializer\Adapter\MessagePack();
                $this->fail("Zend\\Serializer\\Adapter\\MessagePack needs missing ext/msgpack but did't throw exception");
            } catch (ExtensionNotLoadedException $e) {}
            $this->markTestSkipped('Zend\\Serializer\\Adapter\\MessagePack needs ext/msgpack');
        }
        $this->adapter = new Serializer\Adapter\MessagePack();
    }

    public function tearDown()
    {
        $this->adapter = null;
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = msgpack_pack($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = msgpack_pack($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = msgpack_pack($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = msgpack_pack($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value    = new \stdClass();
        $expected = msgpack_pack($value);

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $expected = 'test';
        $value    = msgpack_pack($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $expected = false;
        $value    = msgpack_pack($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull()
    {
        $expected = null;
        $value    = msgpack_pack($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $expected = 100;
        $value    = msgpack_pack($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject()
    {
        $expected = new \stdClass();
        $value    = msgpack_pack($expected);

        $data = $this->adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeInvalid()
    {
        $value = "";
        $this->setExpectedException(
            'Zend\Serializer\Exception\RuntimeException',
            'Unserialization failed'
        );
        $res = $this->adapter->unserialize($value);
    }
}