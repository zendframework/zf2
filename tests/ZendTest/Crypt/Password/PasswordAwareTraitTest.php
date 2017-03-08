<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Password;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @requires PHP 5.4
 */
class PasswordAwareTraitTest extends TestCase
{
    /**
     * Verify basic behavior of setPassword().
     *
     * @return void
     */
    public function testSetPassword()
    {
        $object = $this->getObjectForTrait('\Zend\Crypt\Password\PasswordAwareTrait');
        $this->assertAttributeEquals(null, 'password', $object);
        $password = new TestAsset\SimplePassword();
        $object->setPassword($password);
        $this->assertAttributeEquals($password, 'password', $object);
    }

    /**
     * Verify basic behavior of getPassword().
     *
     * @return void
     */
    public function testGetPassword()
    {
        $object = $this->getObjectForTrait('\Zend\Crypt\Password\PasswordAwareTrait');
        $this->assertNull($object->getPassword());
        $password = new TestAsset\SimplePassword();
        $object->setPassword($password);
        $this->assertEquals($password, $object->getPassword());
    }
}
