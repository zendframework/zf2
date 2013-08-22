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
 * This filter evaluates to true if a method have no parameters or only optional parameters
 */
class OptionalParametersFilter implements FilterInterface
{
    /**
     * Map of methods already analyzed by {@see \Zend\Hydrator\Filter\OptionalParametersFilter::filter()},
     * cached for performance reasons
     *
     * @var array|bool[]
     */
    private static $propertiesCache = array();

    /**
     * {@inheritDoc}
     */
    public function filter($property, $context = null)
    {
        if (isset(self::$propertiesCache[$property])) {
            return self::$propertiesCache[$property];
        }

        try {
            $reflectionMethod = new ReflectionMethod($context, $property);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(sprintf('Method %s doesn\'t exist', $property));
        }

        return self::$propertiesCache[$property] = ($reflectionMethod->getNumberOfRequiredParameters() === 0);
    }
}
