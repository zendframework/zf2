<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password;

trait PasswordAwareTrait
{
    /**
     * Password format instance.
     *
     * @var PasswordInterface
     */
    protected $password = null;

    /**
     * Set the password format.
     *
     * @param PasswordInterface $password The new password format
     *
     * @return self
     */
    public function setPassword(PasswordInterface $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Retrieve the password format.
     *
     * @return null|PasswordInterface
     */
    public function getPassword()
    {
        return $this->password;
    }
}
