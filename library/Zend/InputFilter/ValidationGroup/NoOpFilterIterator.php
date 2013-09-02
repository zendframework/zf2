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
 * A very simple validation group filter iterator that accepts everything
 */
class NoOpFilterIterator extends FilterIterator implements FilterIteratorInterface
{
    /**
     * @param InputCollectionInterface $iterator
     */
    public function __construct(InputCollectionInterface $iterator)
    {
        parent::__construct($iterator);
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        return true;
    }
}
