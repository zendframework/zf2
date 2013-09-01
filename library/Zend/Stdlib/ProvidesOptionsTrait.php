<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

/**
 * Simple trait that allows any classes to accept options
 *
 * Options must follow some conventions in order for this to work. According to Zend
 * Framework 2 conventions, keys must be underscore_separated. For each keys, a method
 * following the same name must exist in the class. For instance, the key "first_name"
 * must have a setFirstName method.
 */
trait ProvidesOptionsTrait
{
    /**
     * Set options
     *
     * @param  array $options
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            // Because PHP is case-insensitive, we can ignore some string manipulations
            // to have some performance boost
            $method = 'set' . str_replace('_', '', $key);

            if (!method_exists($this, $method)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Cannot set option "%s", because class "%s" does not have a method called "%s"',
                    $key,
                    __CLASS__,
                    'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))
                ));
            }

            $this->$method($value);
        }
    }
}
