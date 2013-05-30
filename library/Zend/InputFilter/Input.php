<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\Validator\ValidatorChain;
use Zend\Filter\FilterChain;

/**
 * Input
 */
class Input implements InputInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $breakOnFailure = false;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    /**
     * @var FilterChain
     */
    protected $filterChain;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->filterChain->filter($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValue()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function breakOnFailure()
    {
        return $this->breakOnFailure;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilterChain()
    {
        return $this->filterChain;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidatorChain()
    {
        return $this->validatorChain;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        // TODO: Implement isValid() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        // TODO: Implement getErrorMessages() method.
    }
}
