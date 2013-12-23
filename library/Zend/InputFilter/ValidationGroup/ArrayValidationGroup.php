<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\ValidationGroup;

use CallbackFilterIterator;
use Zend\InputFilter\InputCollectionInterface;

/**
 * Validation group filter based on a simple array defined in the input collection
 */
class ArrayFilterIterator implements ValidationGroupInterface
{
    /**
     * @var array
     */
    protected $validationGroup;

    /**
     * @param array $validationGroup
     */
    public function __construct(array $validationGroup)
    {
        $this->validationGroup = $validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function createFilterIterator(InputCollectionInterface $inputCollection)
    {
        $callback = function($value, $key) {
            return in_array($key, $this->validationGroup);
        };

        return new CallbackFilterIterator($inputCollection->getIterator(), $callback);
    }
}
