<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter\TestAsset;

use Zend\InputFilter\Input;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 * @subpackage UnitTest
 */
class CustomInputWithOptions extends Input
{
    /**
     * @var string
     */
    protected $doSomethingFunny;

    /**
     * Set options for an input
     *
     * @param  array $options
     * @return mixed
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['do_something_funny'])) {
            $this->setDoSomethingFunny($options['do_something_funny']);
        }
    }

    public function setDoSomethingFunny($doSomethingFunny)
    {
        $this->doSomethingFunny = $doSomethingFunny;
    }

    public function getDoSomethingFunny()
    {
        return $this->doSomethingFunny;
    }
}
