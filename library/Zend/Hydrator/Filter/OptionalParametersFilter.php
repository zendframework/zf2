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
 * This filter accepts any method that have only optional parameters
 */
class OptionalParametersFilter implements FilterInterface
{
    /**
     * Map of methods already analyzed by {@see \Zend\Hydrator\Filter\OptionalParametersFilter::filter()},
     * cached for performance reasons
     *
     * @var array|bool[]
     */
    private static $propertiesCache = [];

    /**
     * {@inheritDoc}
     */
    public function accept($property, ExtractionContext $context = null)
    {
        if (isset(self::$propertiesCache[$property])) {
            return self::$propertiesCache[$property];
        }

        $pos      = strpos($property, '::');
        $pos      = $pos !== false ? $pos + 2 : 0;
        $property = substr($property, $pos);

        try {
            $reflectionMethod = new ReflectionMethod($context->getObject(), $property);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(sprintf('Method "%s" does not exist', $property));
        }

        return self::$propertiesCache[$property] = ($reflectionMethod->getNumberOfRequiredParameters() === 0);
    }
}
