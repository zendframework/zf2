<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator;

/**
 * This hydrator uses the getArrayCopy/exchangeArray to extract/hydrate an object, respectively
 */
class ArraySerializableHydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        if (!is_callable([$object, 'getArrayCopy'])) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided object to implement getArrayCopy()', __METHOD__
            ));
        }

        $data = $object->getArrayCopy();

        foreach ($data as $property => $value) {
            if (!$this->compositeFilter->accept($property, $object)) {
                unset($data[$property]);
                continue;
            }

            $property        = $this->namingStrategy->getNameForExtraction($property, $object);
            $data[$property] = $this->extractValue($property, $value, $object);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        array_walk($data, function (&$value, $property) use ($data) {
            $property = $this->namingStrategy->getNameForHydration($property, $data);
            $value    = $this->hydrateValue($property, $value, $data);
        });

        if (is_callable([$object, 'exchangeArray'])) {
            $object->exchangeArray($data);
        } elseif (is_callable([$object, 'populate'])) {
            $object->populate($data);
        } else {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided object to implement exchangeArray() or populate()', __METHOD__
            ));
        }

        return $object;
    }
}
