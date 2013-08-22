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
 * This very simple hydrator uses the public variables of an object.
 */
class ObjectPropertyHydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $data = get_object_vars($object);

        foreach ($data as $property => $value) {
            if (!$this->compositeFilter->accept($property, $object)) {
                unset($data[$property]);
                continue;
            }

            $data[$property] = $this->extractValue($property, $value, $object);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        foreach ($data as $property => $value) {
            $object->$property = $this->hydrateValue($property, $value, $data);
        }

        return $object;
    }
}
