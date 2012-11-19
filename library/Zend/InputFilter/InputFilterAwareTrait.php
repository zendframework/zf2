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

use Zend\InputFilter\InputFilterInterface;

/**
 * @category  Zend
 * @package   Zend_InputFilter
 */
trait InputFilterAwareTrait
{
    /**
     * @var InputFilterInterface
     */
    protected $inputFilter = null;

    /**
     * setInputFilter
     *
     * @param InputFilterInterface $inputFilter
     * @return mixed
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;

        return $this;
    }

    /**
     * getInputFilter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}
