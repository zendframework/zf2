<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use RecursiveIteratorIterator;

/**
 * This RecursiveIteratorIterator is specialized so that it can automatically
 * fills error messages, while keeping the original hierarchy structure of the
 * input filter
 */
class InputCollectionRecursiveIteratorIterator extends RecursiveIteratorIterator
{
    /**
     * Data that is used for the traversal
     *
     * @var array
     */
    protected $data = array();

    /**
     * Aggregated error messages
     *
     * @var array
     */
    protected $errorMessages = array();

    /**
     * Error messages for the current iteration
     *
     * @var array
     */
    protected $currentErrorMessages = array();

    /**
     * Prepare the traversal by setting the data and error messages array
     *
     * @param array $data
     * @param array $errorMessages
     */
    public function prepareTraversal(array &$data, array &$errorMessages)
    {
        $this->data                 = &$data;
        $this->currentErrorMessages = &$errorMessages;
    }

    /**
     * Get the error messages
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @return void
     */
    public function endIteration()
    {
        //$this->currentErrorMessages = $this->errorMessages;
    }

    /**
     * Start to traverse an input collection
     *
     * @return void
     */
    public function beginChildren()
    {
        $this->data = next($this->data);
        /** @var \Zend\InputFilter\InputCollectionInterface $inputCollection */
        //$inputCollection = $this->getSubIterator()->getInnerIterator();
        //$this->currentErrorMessages[$inputCollection->getName()] = array();
        //$this->currentErrorMessages = next($this->currentErrorMessages);
    }

    /**
     * Finish to traverse an input collection
     *
     * @return void
     */
    public function endChildren()
    {
        if (is_array($this->data)) {
            $this->data = prev($this->data);
        }

        /** @var \Zend\InputFilter\InputCollectionInterface $inputCollection */
        //$inputCollection = $this->getSubIterator()->getInnerIterator();
        //$this->errorMessages[$inputCollection->getName()] = $this->currentErrorMessages;

    }
}
