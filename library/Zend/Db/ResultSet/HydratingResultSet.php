<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\ResultSet;

use Iterator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ArraySerializable;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage ResultSet
 */
class HydratingResultSet extends ResultSet
{
    /**
     * @var HydratorInterface
     */
    protected $rowObjectHydrator = null;

    /**
     * Constructor
     * 
     * @param  null|object $rowObjectPrototype 
     * @param  null|HydratorInterface $rowObjectHydrator 
     * @return void
     */
    public function __construct($rowObjectPrototype = null, HydratorInterface $rowObjectHydrator = null)
    {
        $this->setRowObjectHydrator(($rowObjectHydrator) ?: new ArraySerializable);
        parent::__construct($rowObjectPrototype);
    }

    /**
     * Set the row object prototype
     * 
     * @param  object $rowObjectPrototype 
     * @return ResultSet
     */
    public function setRowObjectPrototype($rowObjectPrototype)
    {
        $this->rowObjectPrototype = $rowObjectPrototype;
        return $this;
    }

    /**
     * Set the hydrator to use for each row object
     *
     * @param HydratorInterface $rowObjectHydrator
     */
    public function setRowObjectHydrator(HydratorInterface $rowObjectHydrator)
    {
        $this->rowObjectHydrator = $rowObjectHydrator;
        return $this;
    }

    /**
     * Get the hydrator to use for each row object
     *
     * @return rowObjectHydrator
     */
    public function getRowObjectHydrator()
    {
        return $this->rowObjectHydrator;
    }

    /**
     * Iterator: get current item
     * 
     * @return array|RowObjectInterface
     */
    public function current()
    {
        $data = $this->dataSource->current();

        if ($this->returnType === self::TYPE_OBJECT && is_array($data)) {
            $row = clone $this->rowObjectPrototype;
            return $this->getRowObjectHydrator()->hydrate($data, $row);
        } else {
            return $data;
        }
    }

}
