<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password\Algorithm;

use Zend\Crypt\Password\HandlerInterface;

/**
 * Fallback for old databases with simple SHA1 hashes.
 *
 * This handler can be used with old databases which have their passwords hashed
 * with SHA1. To make the hashes in your database compatible with this handler,
 * simply prefix all password hashes with "$simple-sh1$".
 */
class SimpleSha1 implements HandlerInterface
{
    /**
     * supports(): defined by HandlerInterface.
     *
     * @see    HandlerInterface::supports()
     * @param  string $hash
     * @return boolean
     */
    public function supports($hash)
    {
        return (bool) preg_match('(^\$simple-sha1\$[a-zA-Z0-9]{40}$)', $hash);
    }

    /**
     * hash(): defined by HandlerInterface.
     *
     * @see    HandlerInterface::hash()
     * @param  string $password
     * @return string
     */
    public function hash($password)
    {
        return '$simple-sha1$' . sha1($password);
    }

    /**
     * compare(): defined by HandlerInterface.
     *
     * @see    HandlerInterface::compare()
     * @param  string $password
     * @param  string $hash
     * @return boolean
     */
    public function compare($password, $hash)
    {
        return ('$simple-sha1$' . sha1($password) === $hash);
    }

    /**
     * shouldRehash(): defined by HandlerInterface.
     *
     * @see    HandlerInterface::shouldRehash()
     * @param  string $hash
     * @return boolean
     */
    public function shouldRehash($hash)
    {
        return false;
    }
}
