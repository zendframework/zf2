<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace Zend\InputFilter;

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;
use Zend\Validator\NotEmpty;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
class FileInput extends Input
{
    /**
     * @var boolean
     */
    protected $isValid = false;

    /**
     * @return mixed
     */
    public function getValue()
    {
        $filter = $this->getFilterChain();
        $value  = (isset($this->value['tmp_name']))
                ? $this->value['tmp_name'] : $this->value;
        if ($this->isValid) {
            $value = $filter->filter($value);
        }
        return $value;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator
     * @return boolean
     */
    public function isValid($context = null)
    {
        $this->injectNotEmptyValidator();
        $validator = $this->getValidatorChain();
        //$value     = $this->getValue(); // Do not run the filters yet for File uploads
        $this->isValid = $validator->isValid($this->getRawValue(), $context);
        return $this->isValid;
    }

    protected function injectNotEmptyValidator()
    {
        $this->notEmptyValidator = true;
        // TODO: Could do something like automatically add the Upload validator here
    }
}
