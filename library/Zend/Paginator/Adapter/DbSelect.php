<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator\Adapter;

use Zend\Db\Sql;

/**
 * @category   Zend
 * @package    Zend_Paginator
 */
class DbSelect implements AdapterInterface
{
    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * The COUNT query
     *
     * @var Sql\Select
     */
    protected $countSelect = null;

    /**
     * Adapter Options
     *
     * @var DbSelectOptions
     */
    protected $options = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $rowCount = null;

    /**
     * Constructor.
     *
     * @param array|DbSelectOptions $select The Select Query object
     */
    public function __construct($options)
    {
        if ( ! $options instanceof DbSelectOptions ) {
            $options = new DbSelectOptions($options);
        }
        $this->options = $options;
    }

    /**
     * Sets the total row count, either directly or through a supplied
     * query.  Without setting this, {@link getPages()} selects the count
     * as a subquery (SELECT COUNT ... FROM (SELECT ...)).  While this
     * yields an accurate count even with queries containing clauses like
     * LIMIT, it can be slow in some circumstances.  For example, in MySQL,
     * subqueries are generally slow when using the InnoDB storage engine.
     * Users are therefore encouraged to profile their queries to find
     * the solution that best meets their needs.
     *
     * @param  Sql\Select|integer $rowCount Total row count integer
     *                                               or query
     * @throws Exception\InvalidArgumentException
     * @return DbSelect
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Sql\Select) {
            $columns = $rowCount->getRawState('columns');
            $countColumn = $columns[0];
            if ($countColumn instanceof Sql\ExpressionInterface) {
                $countColumn = $countColumn->getExpressionData();

                // The select query can contain only one column, which should be the row count column
                if(false === strpos($countColumn[0][0], self::ROW_COUNT_COLUMN)) {
                    throw new Exception\InvalidArgumentException('Row count column not found');
                } 
            }

            $dbAdapter      = $this->options->getDbAdapter();
            $dbPlatform     = $dbAdapter->getPlatform();
            $sqlString      = $rowCount->getSqlString($dbPlatform);
            $result         = $dbAdapter->query($sqlString)->execute();
            $this->rowCount = count($result);
        } elseif (is_integer($rowCount)) {
            $this->rowCount = $rowCount;
        } else {
            throw new Exception\InvalidArgumentException('Invalid row count');
        }

        return $this;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset           Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $select = $this->options->getSelectQuery();
        $select->limit($itemCountPerPage)->offset($offset);

        $sql        = new Sql\Sql($this->options->dbAdapter);
        $statement  = $sql->prepareStatementForSqlObject($select);
        $result     = $statement->execute();
        $resultset  = $this->options->getResultSetPrototype()->initialize($result);
        return $resultset->toArray();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->rowCount === null) {
            $this->setRowCount(
                $this->getCountSelect()
            );
        }

        return $this->rowCount;
    }

    /**
     * Get the COUNT select object for the provided query
     *
     * TODO: Have a look at queries that have both GROUP BY and DISTINCT specified.
     * In that use-case I'm expecting problems when either GROUP BY or DISTINCT
     * has one column.
     *
     * @return Sql\Select
     */
    public function getCountSelect()
    {
        /**
         * We only need to generate a COUNT query once. It will not change for
         * this instance.
         */
        if ($this->countSelect !== null) {
            return $this->countSelect;
        }

        $rowCount = clone $this->options->getSelectQuery();
        $dbAdapter = $this->options->getDbAdapter();

        // Reset columns to contain only a COUNT(1) column
        $rowCount->columns(array(
             new Sql\Expression('COUNT(1) AS ' . $dbAdapter->getPlatform()->quoteIdentifier(self::ROW_COUNT_COLUMN))
        ));

        $this->countSelect = $rowCount;

        return $rowCount;
    }
}
