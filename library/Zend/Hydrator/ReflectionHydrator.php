<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator;

use ReflectionClass;
use Zend\Hydrator\Context\ExtractionContext;
use Zend\Hydrator\Context\HydrationContext;

/**
 * This hydrator uses Reflection API to extract/hydrate
 */
class ReflectionHydrator extends AbstractHydrator
{
    /**
     * An in-array cache, indexed by object name
     *
     * @var array
     */
    protected static $reflProperties = array();

    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $result  = [];
        $context = new ExtractionContext($object);

        /* @var \ReflectionProperty $property */
        foreach (self::getReflProperties($object) as $property) {
            $propertyName = $this->namingStrategy->getNameForExtraction($property->getName(), $context);

            if (!$this->compositeFilter->accept($property, $context)) {
                continue;
            }

            $value                 = $property->getValue($object);
            $result[$propertyName] = $this->extractValue($propertyName, $value, $context);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        $reflProperties = self::getReflProperties($object);
        $context        = new HydrationContext($data, $object);

        foreach ($data as $property => $value) {
            $property = $this->namingStrategy->getNameForHydration($property, $context);

            if (isset($reflProperties[$property])) {
                $reflProperties[$property]->setValue($object, $this->hydrateValue($property, $value, $context));
            }
        }
        return $object;
    }

    /**
     * Get a reflection properties from in-memory cache and lazy-load if class has not been loaded
     *
     * @param  object $object
     * @return array
     */
    protected static function getReflProperties($object)
    {
        $className = get_class($object);

        if (isset(static::$reflProperties[$className])) {
            return static::$reflProperties[$className];
        }

        static::$reflProperties[$className] = array();
        $reflClass                          = new ReflectionClass($className);
        $reflProperties                     = $reflClass->getProperties();

        foreach ($reflProperties as $property) {
            $property->setAccessible(true);
            static::$reflProperties[$className][$property->getName()] = $property;
        }

        return static::$reflProperties[$className];
    }
}
