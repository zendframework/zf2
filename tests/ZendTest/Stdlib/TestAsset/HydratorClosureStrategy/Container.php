<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset\HydratorClosureStrategy;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Container implements InputFilterAwareInterface
{
    public $entities; // public to make testing easier!
    private $inputFilter; // used to test forms

    public function __construct()
    {
        $this->entities = array();
    }

    public function addEntity(SimpleEntity $entity)
    {
        $this->entities[] = $entity;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $input = new Input();
            $input->setName('entities');
            $input->setRequired(false);

            $this->inputFilter = new InputFilter();
            $this->inputFilter->add($input);
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }

    // Add the getArrayCopy method so we can test the ArraySerializable hydrator:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add the populate method so we can test the ArraySerializable hydrator:
    public function populate($data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}
