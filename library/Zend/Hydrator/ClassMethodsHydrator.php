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
 * code and your conventions. For instance, it will only check get/is/has methods for
 * extraction, and set methods for hydration. It also assumes that object properties
 * are camelCased, which is PSR-1 convention.
 *
 * If you have very specific use cases, you are encouraged to create your own hydrator
 */
class ClassMethodsHydrator extends AbstractHydrator
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // NOTE: In ZF2 we attached a lot more filters. However, there is now no need
        // as we use preg_filter, which automatically returns NULL if the method does not
        // match a specific pattern. We only need to make sure that it does not need
        // any required parameters
    }

    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $methods = get_class_methods($object);
        $result  = array();

        foreach ($methods as $method) {
            $method = preg_filter(array('/get/', '/is/', '/has/'), '', $method);

            if (null === $method || !$this->compositeFilter->filter($method)) {
                continue;
            }

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
