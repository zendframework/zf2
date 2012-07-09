<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset\Authentication;

use Zend\Amf\AbstractAuthentication,
    Zend\Authentication\Result;

class WrongPassword extends AbstractAuthentication
{
    public function authenticate() {
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            array('Wrong Password')
        );
    }
}


