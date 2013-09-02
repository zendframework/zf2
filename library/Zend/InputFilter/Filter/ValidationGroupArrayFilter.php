<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\Filter;

use Zend\InputFilter\InputCollectionInterface;

/**
 * Validation group filter based on a simple array defined in the input collection
 */
class ValidationGroupArrayFilter extends AbstractValidationGroupFilter
{
    /**
     * @var array
     */
    protected $validationGroup;

    /**
     * @param InputCollectionInterface $recursiveIterator
     * @param array                    $validationGroup
     */
    public function __construct(InputCollectionInterface $recursiveIterator, array $validationGroup = array())
    {
        parent::__construct($recursiveIterator);

        // This is an optimization, this way we can check using isset, which is way faster than
        // in_array (especially with very large arrays)
        $this->validationGroup = array_flip($validationGroup);
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        return true; //isset($this->validationGroup[$this->key()]);
    }
}
