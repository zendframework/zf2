<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Message
{
    public function isValid()
    {
        return true;
    }

    public function headers()
    {
    }

    public function addFrom($emailOrAddressOrList, $name = null)
    {
    }

    public function from()
    {
    }

    public function addTo($emailOrAddressOrList, $name = null)
    {
    }

    public function to()
    {
    }

    public function addCc($emailOrAddressOrList, $name = null)
    {
    }

    public function cc()
    {
    }

    public function addBcc($emailOrAddressOrList, $name = null)
    {
    }

    public function bcc()
    {
    }

    public function addReplyTo($emailOrAddressOrList, $name = null)
    {
    }

    public function replyTo()
    {
    }

    public function setSender($emailOrAddress, $name = null)
    {
    }

    public function getSender()
    {
    }

    public function setSubject($subject)
    {
    }

    public function getSubject()
    {
    }

    public function setBody($body)
    {
    }

    public function getBody()
    {
    }

    public function getBodyText()
    {
    }
}
