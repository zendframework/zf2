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
     * @param  Zend\Db\Sql\Select|integer $rowCount Total row count integer
     *                                               or query
     * @throws Exception\InvalidArgumentException
     * @return DbSelect
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Sql\Select) {
            $rowCountColumn = self::ROW_COUNT_COLUMN;
            $dbAdapter      = $this->options->getDbAdapter();
            $dbPlatform     = $dbAdapter->getPlatform();
            $sqlString      = $rowCount->getSqlString($dbPlatform);

            // The select query can contain only one column, which should be the row count column
            if (false === strpos($sqlString, $rowCountColumn)) {
                throw new Exception\InvalidArgumentException('Row count column not found');
            }

            $result         = $dbAdapter->query($sqlString)->execute()->current();
            $this->rowCount = count($result) > 0 ? $result[$rowCountColumn] : 0;
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
        $resultSet  = $this->options->getResultSetPrototype()->initialize($result);
        return $resultSet->toArray();
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
        $dbPlatform = $dbAdapter->getPlatform();


        // Reset columns to contain only a COUNT(1) columntform = $dbAdapter->getPlatform();
        $countColumn = $dbPlatform->quoteIdentifier(self::ROW_COUNT_COLUMN);
        $countPart   = 'COUNT(1) AS ';
        $groupPart   = null;

        $columnPart = $rowCount->getRawState('columns');
        $tablePart = $rowCount->getRawState('table');
        $groupParts  = $rowCount->getRawState('group');
        $havingParts = $rowCount->getRawState('having');

        /**
         * If there is more than one column AND it's a DISTINCT query, more
         * than one group, or if the query has a HAVING clause, then take
         * the original query and use it as a subquery os the COUNT query.
         */
        if (count($groupParts) > 1 || ($havingParts->count() > 0)) {
            $sqlString = $rowCount->getSqlString($dbPlatform);

            $tablePart = array('t' => $sqlString);

        } else if (!empty($groupParts) && $groupParts[0] !== Sql\Select::SQL_STAR &&
            !($groupParts[0] instanceof Sql\ExpressionInterface)
        ) {
            $groupPart = $dbPlatform->quoteIdentifierInFragment($groupParts[0]);
        }

        /**
         * If the original query had a GROUP BY or a DISTINCT part and only
         * one column was specified, create a COUNT(DISTINCT ) query instead
         * of a regular COUNT query.
         */
        if (!empty($groupPart)) {
            $countPart = 'COUNT(DISTINCT ' . $groupPart . ') AS ';
        }

        /**
         * Create the COUNT part of the query
         */
        $expression = new Sql\Expression($countPart . $countColumn);
        $select = new Sql\Select();
        $select->from($tablePart)
               ->columns(array($expression));
        return $select;
    }
}
