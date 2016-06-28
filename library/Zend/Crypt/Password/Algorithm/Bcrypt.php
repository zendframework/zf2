<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password\Algorithm;

use Zend\Crypt\Password\Bcrypt as BcryptHasher;
use Zend\Crypt\Password\HandlerInterface;

/**
 * Password hashing handler backed by Bcrypt.
 */
class Bcrypt implements HandlerInterface
{
    /**
     * @var BcryptHasher
     */
    protected $backend;

    /**
     * supports(): defined by HandlerInterface.
     *
     * @see    HandlerInterface::supports()
     * @param  string $hash
     * @return boolean
     */
    public function supports($hash)
    {
        return (bool) preg_match('(^\$2[axy]?\$\d{2}\$[./a-zA-Z0-9]{53}$)', $hash);
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
        return $this->getBackend()->create($password);
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
        return $this->getBackend()->verify($password, $hash);
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
        $cost   = $this->getBackend()->getCost();
        $values = explode('$', $hash);

        if (count($values) < 3) {
            // Something is broken, this hash clearly needs to be re-hashed.
            return true;
        }

        return ($values[2] !== $cost);
    }

    /**
     * @return BcryptHasher
     */
    public function getBackend()
    {
        if ($this->backend === null) {
            $this->backend = new BcryptHasher();
        }

        return $this->backend;
    }

    /**
     * @param  BcryptHasher $backend
     * @return Bcrypt
     */
    public function setBackend(BcryptHasher $backend)
    {
        $this->backend = $backend;
        return $this;
    }
}
