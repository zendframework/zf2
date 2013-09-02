<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\Iterator;

use RecursiveIteratorIterator;

/**
 * This RecursiveIteratorIterator is specialized so that it can automatically
 * fills error messages, while keeping the original hierarchy structure of the
 * input filter
 */
class InputCollectionRecursiveIteratorIterator extends RecursiveIteratorIterator
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var mixed|null
     */
    protected $context;

    /**
     * Must the recursion be stopped (for breaking failure for instance)?
     *
     * @var bool
     */
    protected $isStopped = false;

    /**
     * Prepare the recursive iterator iterator for the traversal
     *
     * @param array      $data
     * @param mixed|null $context
     */
    public function prepareTraversal(array $data, $context = null)
    {
        $this->data    = $data;
        $this->context = $context;
    }

    public function beginChildren()
    {
        var_dump('BEGIN CHILDREN');
        //$this->errorMessages[]
    }

    public function endChildren()
    {
        var_dump('END CHILDREN');
    }

    /**
     *
     */
    public function nextElement()
    {
        /** @var \Zend\InputFilter\InputInterface $input */
        $input = $this->current();
        $name  = $input->getName();

        //$this->errorMessages[$name] $input->validate($this->data[$input->getName()]);
    }
}
