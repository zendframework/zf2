<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Constraint;

abstract class AbstractConstraint implements ConstraintInterface
{
    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @param string|array $columns
     */
    public function __construct($columns = null)
    {
        (!$columns) ?: $this->setColumns($columns);
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

}
