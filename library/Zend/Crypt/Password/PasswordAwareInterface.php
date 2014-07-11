<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password;

interface PasswordAwareInterface
{
    /**
     * Set the password format.
     *
     * @param PasswordInterface $password The new password format.
     *
     * @return PasswordAwareInterface
     */
    public function setPassword(PasswordInterface $password);

    /**
     * Retrieve the password format.
     *
     * @return PasswordInterface
     */
    public function getPassword();
}
