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
use Zend\Hydrator\Context\ExtractionContext;
use Zend\Hydrator\Exception\InvalidArgumentException;

/**
 * This filter accepts any method that have a given number of accepted parameters
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
    public function accept($property, ExtractionContext $context = null)
    {
        $pos      = strpos($property, '::');
        $pos      = $pos !== false ? $pos + 2 : 0;
        $property = substr($property, $pos);

        try {
            $reflectionMethod = new ReflectionMethod($context->getObject(), $property);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(sprintf('Method "%s" does not exist', $property));
        }

        return $reflectionMethod->getNumberOfParameters() === $this->numberOfParameters;
    }
}
