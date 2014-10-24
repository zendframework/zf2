<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator;

class SimpleArraySerializable implements HydratorInterface
{

    /**
     * Extract values from the provided object
     *
     * Extracts values via the object's getArrayCopy() method.
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $data = $object->getArrayCopy();

        return $data;
    }

    /**
     * Hydrate an object
     *
     * Hydrates an object by passing $data to either its exchangeArray() method.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $object->exchangeArray($data);

        return $object;
    }
}
