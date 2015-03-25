<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail;

class Address implements Address\AddressInterface
{
    protected $email;
    protected $name;

    /**
     * Constructor
     *
     * @param  string $email
     * @param  null|string $name
     * @throws Exception\InvalidArgumentException
     * @return Address
     */
    public function __construct($email, $name = null)
    {
        if (!is_string($email)) {
            throw new Exception\InvalidArgumentException('Email must be a string');
        }
        if (null !== $name && !is_string($name)) {
            throw new Exception\InvalidArgumentException('Name must be a string');
        }
        if (strlen($email) > 254) {
            // see http://www.rfc-editor.org/errata_search.php?eid=1690
            throw new Exception\InvalidArgumentException('Email max size is 254 chars');
        }
        $arr = explode('@', $email);
        if (isset($arr[0]) && strlen($arr[0]) > 64) {
            // http://tools.ietf.org/html/rfc5321#section-4.5.3.1.1
            throw new Exception\InvalidArgumentException('Email local part max size is 64 chars');
        }
        if (isset($arr[1]) && strlen($arr[1]) > 255) {
            // http://tools.ietf.org/html/rfc5321#section-4.5.3.1.2
            throw new Exception\InvalidArgumentException('Email domain part max size is 255 chars');
        }

        $this->email = $email;
        $this->name  = $name;
    }

    /**
     * Retrieve email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * String representation of address
     *
     * @return string
     */
    public function toString()
    {
        $string = '<' . $this->getEmail() . '>';
        $name   = $this->getName();
        if (null === $name) {
            return $string;
        }

        $string = $name . ' ' . $string;
        return $string;
    }
}
