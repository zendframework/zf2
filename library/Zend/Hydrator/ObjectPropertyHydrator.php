<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ObjectPropertyHydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     */
    public function extract($object, $recursive = true)
    {
        $iterator          = new RecursiveArrayIterator(get_object_vars($object));
        $recursiveIterator = new RecursiveIteratorIterator($iterator);
        $recursiveIterator->setMaxDepth($recursive ? -1 : 0);

        if ($recursive) {
            $iterator          = new RecursiveArrayIterator(get_object_vars($object));
            $recursiveIterator = new RecursiveIteratorIterator($iterator);

            foreach ($recursiveIterator as $key => $valueOrObject) {

            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object, $recursive = true)
    {
        if ($recursive) {
            foreach ($data as $property => $value) {
                if (is_array($value) && $data->$property) {
                    $this->hydrate($value, $data->$property, true);
                }

                $object->$property = $value;
            }
        } else {
            foreach ($data as $property => $value) {
                $object->$property = $value;
            }
        }
    }
}
