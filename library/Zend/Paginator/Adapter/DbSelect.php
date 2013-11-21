<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;

class DbSelect implements AdapterInterface
{

    const ROW_COUNT_COLUMN_NAME = 'c';

    /**
     * @var Sql
     */
    protected $sql = null;

    /**
     * Database query
     *
     * @var Select
     */
    protected $select = null;

    /**
     * Database count query
     *
     * @var Select
     */
    protected $selectCount = null;

    /**
     * @var ResultSet
     */
    protected $resultSetPrototype = null;

    /**
     * Total item count
     *
     * @var int
     */
    protected $rowCount = null;

    /**
     * Constructor.
     *
     * @param Select $select The select query
     * @param Adapter|Sql $adapterOrSqlObject DB adapter or Sql object
     * @param null|ResultSetInterface $resultSetPrototype
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(Select $select, $adapterOrSqlObject, ResultSetInterface $resultSetPrototype = null)
    {
        $this->select = $select;

        if ($adapterOrSqlObject instanceof Adapter) {
            $adapterOrSqlObject = new Sql($adapterOrSqlObject);
        }

        if (!$adapterOrSqlObject instanceof Sql) {
            throw new Exception\InvalidArgumentException(
                '$adapterOrSqlObject must be an instance of Zend\Db\Adapter\Adapter or Zend\Db\Sql\Sql'
            );
        }

        $this->sql                = $adapterOrSqlObject;
        $this->resultSetPrototype = ($resultSetPrototype) ?: new ResultSet;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  int $offset           Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $select = clone $this->select;
        $select->offset($offset);
        $select->limit($itemCountPerPage);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return int
     */
    public function count()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }

        $select = $this->getSelectCount();

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        $row       = $result->current();

        $this->rowCount = (int) $row[self::ROW_COUNT_COLUMN_NAME];

        return $this->rowCount;
    }

    /**
     * Returns select query for count
     *
     * @return Select
     */
    protected function getSelectCount()
    {
        if ($this->selectCount !== null) {
            return $this->selectCount;
        }

        $select = clone $this->select;
        $select->reset(Select::LIMIT);
        $select->reset(Select::OFFSET);
        $select->reset(Select::ORDER);

        $countSelect = new Select;
        $countSelect->columns(array(self::ROW_COUNT_COLUMN_NAME => new Expression('COUNT(1)')));
        $countSelect->from(array('original_select' => $select));

        return $countSelect;
    }

    /**
     * Sets custom select to count all entries in db
     *
     * @param Select $select
     * @return self
     */
    public function setSelectCount(Select $select)
    {
        $this->select = $select;

        return $this;
    }
}
