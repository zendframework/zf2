<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session\TestAsset;

use Zend\Session\Validator\ValidatorInterface;

class TestFailingValidator implements ValidatorInterface
{
    
    public function getData() {
        return false;
    }

    public function getName() {
        return __CLASS__;
    }

    public function isValid() {
        return $this->getData();
    }
}
