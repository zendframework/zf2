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
 * An ArrayInputCollection is a specialized InputCollection that can run the same filters
 * and validators to an array of data
 */
class ArrayInputCollection extends InputCollection
{
    /**
     * {@inheritDoc}
     */
    public function runAgainst($data, $context = null)
    {
        if (is_array($data)) {
            throw new Exception\RuntimeException(sprintf(
                'Data provided must be an array, "%s" given',
                gettype($data)
            ));
        }

        foreach ($data as $key => $value) {
            // @TODO: handle the merging of validation result
            $validationResult = parent::runAgainst($value);
        }

        return $validationResult;
    }
}
