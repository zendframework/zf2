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
 * This is a specialized input filter that can be used to filter a set of array data, using
 * the same base input filter
 */
class CollectionInputFilter
{
    /**
     * @var InputFilterInterface
     */
    protected $innerInputFilter;

    /**
     * @param InputFilterInterface $inputFilter
     */
    public function __construct(InputFilterInterface $inputFilter)
    {
        $this->innerInputFilter = $inputFilter;
    }
}
