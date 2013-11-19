<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

/**
 * An ArrayInput is a specialized Input class that can filter and validate an array of the
 * same value, using the same filters and validation rules
 */
class ArrayInput extends Input
{
    /**
     * {@inheritDoc}
     */
    public function runAgainst($data, $context = null)
    {
        if (is_array($data)) {
            throw new Exception\RuntimeException(sprintf(
                'Value provided must be an array, "%s" given',
                gettype($data)
            ));
        }

        foreach ($data as $key => $value) {
            // @TODO: handle a merging of validation results
            $validationResult = parent::runAgainst($value, $context);
        }

        return $validationResult;
    }
}
