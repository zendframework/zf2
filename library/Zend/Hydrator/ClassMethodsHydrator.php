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
 * This hydrator uses getter/setter methods to extract/hydrate, respectively
 *
 * To keep this hydrator as efficient as possible, it makes some assumptions about your
 * code and your conventions. For instance, it will only check get/is methods for
 * extraction, and set methods for hydration. It also assumes that object properties
 * are camelCased, which is PSR-1 convention.
 *
 * If you have very specific use cases, you are encouraged to create your own hydrator
 */
class ClassMethodsHydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $methods = $this->filterData(get_class_methods($object));
        $result  = array();

        foreach ($methods as $method) {
            $method                      = preg_replace(array('/get/', '/is/'), '', $method);
            $result[strtolower($method)] = $object->$method();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        foreach ($data as $property => $value) {
            $method = 'set' . $property; // No need to uppercase, PHP is case insensible

            if (method_exists($object, $method)) {
                $value = $this->hydrateValue($property, $value, $data);
                $object->$method($value);
            }
        }

        return $object;
    }
}
