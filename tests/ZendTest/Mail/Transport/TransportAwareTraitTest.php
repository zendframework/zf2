<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Transport;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @requires PHP 5.4
 */
class TransportAwareTraitTest extends TestCase
{
    /**
     * Verify basic behavior of setTransport().
     *
     * @return void
     */
    public function testSetTransport()
    {
        $object = $this->getObjectForTrait('\Zend\Mail\Transport\TransportAwareTrait');
        $this->assertAttributeEquals(null, 'mailTransport', $object);
        $mailTransport = new \Zend\Mail\Transport\Null();
        $object->setTransport($mailTransport);
        $this->assertAttributeEquals($mailTransport, 'mailTransport', $object);
    }

    /**
     * Verify basic behavior of getTransport().
     *
     * @return void
     */
    public function testGetTransport()
    {
        $object = $this->getObjectForTrait('\Zend\Mail\Transport\TransportAwareTrait');
        $this->assertNull($object->getTransport());
        $mailTransport = new \Zend\Mail\Transport\Null();
        $object->setTransport($mailTransport);
        $this->assertEquals($mailTransport, $object->getTransport());
    }
}
