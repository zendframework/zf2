<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter\Asset;

use Zend\InputFilter\Input;

class CustomInput extends Input
{
    protected $customOption;

    public function setCustomOption($customOption)
    {
        $this->customOption = $customOption;
    }

    public function getCustomOption()
    {
        return $this->customOption;
    }
} 