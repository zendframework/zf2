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

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
interface InputInterface
{
    public function setAllowEmpty($allowEmpty);
    public function setBreakOnFailure($breakOnFailure);
    public function setErrorMessage($errorMessage);
    public function setFilterChain(FilterChain $filterChain);
    public function setName($name);
    public function setRequired($required);
    public function setValidatorChain(ValidatorChain $validatorChain);
    public function setValue($value);

    public function allowEmpty();
    public function breakOnFailure();
    public function getFilterChain();
    public function getName();
    public function getRawValue();
    public function isRequired();
    public function getValidatorChain();
    public function getValue();

    public function isValid();
    public function getMessages();
}
