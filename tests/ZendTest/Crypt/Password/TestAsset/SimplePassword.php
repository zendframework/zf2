<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Password\TestAsset;

use Zend\Crypt\Password\PasswordInterface;

final class SimplePassword implements PasswordInterface
{
    /**
     * Verify a password hash against a given plain text password
     *
     * @param string $password The password to hash
     * @param string $hash     The supplied hash to validate
     *
     * @return bool Does the password validate against the hash
     */
    public function verify($password, $hash)
    {
        return md5($password) === $hash;
    }

    /**
     * Create a password hash for a given plain text password
     *
     * @param string $password The password to hash
     *
     * @return string The formatted password hash
     */
    public function create($password)
    {
        return md5($password);
    }
}
