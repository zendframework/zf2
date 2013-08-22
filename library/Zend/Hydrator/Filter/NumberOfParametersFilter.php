<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\Filter;

use ReflectionException;
use ReflectionMethod;
use Zend\Hydrator\Exception\InvalidArgumentException;

/**
 * This filter evaluates to true if a method has a given number of parameters
 */
class NumberOfParametersFilter implements FilterInterface
{
    /**
     * The number of parameters being accepted
     *
     * @var int
     */
    protected $numberOfParameters = 0;

    /**
     * @param int $numberOfParameters Number of accepted parameters
     */
    public function __construct($numberOfParameters = 0)
    {
        $this->numberOfParameters = (int) $numberOfParameters;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($property, $context = null)
    {
        try {
            $reflectionMethod = new ReflectionMethod($context, $property);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(
                "Method $property doesn't exist"
            );
        }

        if ($reflectionMethod->getNumberOfParameters() !== $this->numberOfParameters) {
            return false;
        }

        return true;
    }
}
