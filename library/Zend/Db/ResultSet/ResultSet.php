<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\ResultSet;

use ArrayObject;

class ResultSet extends AbstractResultSet
{
    const TYPE_ARRAYOBJECT = 'arrayobject';
    const TYPE_ARRAY = 'array';

    /**
     * @var ArrayObject
     */
    protected $arrayObjectPrototype = null;

    /**
     * Return type to use when returning an object from the set
     *
     * @var ResultSet::TYPE_ARRAYOBJECT|ResultSet::TYPE_ARRAY
     */
    protected $returnType = self::TYPE_ARRAYOBJECT;

    protected $defaultReturnType = self::TYPE_ARRAYOBJECT;

    /**
     * Constructor
     *
     * @param string           $returnType
     * @param null|ArrayObject $arrayObjectPrototype - this parameter id deprecated
     */
    public function __construct($returnType = self::TYPE_ARRAYOBJECT)
    {
        if (func_num_args() == 2 && func_get_arg(1) != null) {
            //backward compatibility
            $this->setArrayObjectPrototype(func_get_arg(1));
        } else {
            $this->setArrayObjectPrototype($returnType);
        }
    }

    /**
     * Set the row object prototype
     *
     * @param  ArrayObject $arrayObjectPrototype
     * @throws Exception\InvalidArgumentException
     * @return ResultSet
     */
    public function setArrayObjectPrototype($arrayObjectPrototype)
    {
        if (in_array($arrayObjectPrototype, array(null, self::TYPE_ARRAY, self::TYPE_ARRAYOBJECT), true)) {
            $this->returnType = $arrayObjectPrototype ?:$this->defaultReturnType;
            if ($this->returnType == self::TYPE_ARRAYOBJECT) {
                $this->arrayObjectPrototype = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            } else {
                $this->arrayObjectPrototype = null;
            }
        } elseif (is_object($arrayObjectPrototype) && method_exists($arrayObjectPrototype, 'exchangeArray')) {
            $this->returnType = self::TYPE_ARRAYOBJECT;
            $this->arrayObjectPrototype = $arrayObjectPrototype;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                'Object must be of type ArrayObject, or at least implement exchangeArray, or must be %s or $s',
                self::TYPE_ARRAYOBJECT,
                self::TYPE_ARRAY
            ));
        }
        return $this;
    }

    /**
     * Get the row object prototype
     *
     * @return ArrayObject
     */
    public function getArrayObjectPrototype()
    {
        return $this->arrayObjectPrototype;
    }

    /**
     * Get the return type to use when returning objects from the set
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    protected function hydrateCurrent()
    {
        $current = parent::hydrateCurrent();
        if (is_object($this->arrayObjectPrototype) && is_array($current)) {
            $ao = clone $this->arrayObjectPrototype;
            $ao->exchangeArray($current);
            return $ao;
        }
        return $current;
    }
}
