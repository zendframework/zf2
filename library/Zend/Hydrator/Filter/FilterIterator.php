<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Hydrator\ExtractorFilter;

use FilterIterator;
use Iterator;

class ExtractorFilterIterator extends FilterIterator
{
    /**
     *
     */
    public function __construct(array $filters, Iterator $iterator)
    {
        parent::__construct($iterator);
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        // TODO: Implement accept() method.
    }
}
