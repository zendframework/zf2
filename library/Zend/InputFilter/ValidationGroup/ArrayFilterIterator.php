<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\ValidationGroup;

use FilterIterator;
use Zend\InputFilter\InputCollectionInterface;

/**
 * Validation group filter based on a simple array defined in the input collection
 */
class ArrayFilterIterator extends FilterIterator implements FilterIteratorInterface
{
    /**
     * @var array
     */
    protected $validationGroup;

    /**
     * @param InputCollectionInterface $inputCollection
     * @param array                    $validationGroup
     */
    public function __construct(InputCollectionInterface $inputCollection, array $validationGroup)
    {
        parent::__construct($inputCollection->getIterator());
        $this->validationGroup = $validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        return in_array($this->key(), $this->validationGroup);
    }
}
