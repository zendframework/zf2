<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace Zend\Paginator\Adapter;

use ArrayObject;
use Zend\Stdlib\AbstractOptions;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Select as SqlSelect;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\ResultSet\ResultSet;

/**
 * Options provider for Zend\Paginator\Adapter\DbSelect
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage Adapter
 */
class DbSelectOptions extends AbstractOptions
{
    /**
     * @var DbAdapter
     */
    protected $db_adapter;

    /**
     * @var SqlSelect
     */
    protected $select_query;

    /**
     * @var ResultSet
     */
    protected $result_set_prototype;

    public function setDbAdapter(DbAdapter $adapter)
    {
        $this->db_adapter = $adapter;
        return $this;
    }

    public function getDbAdapter()
    {
        return $this->db_adapter;
    }

    public function setSelectQuery(SqlSelect $select)
    {
        $this->select_query = $select;
        return $this;
    }

    public function getSelectQuery()
    {
        return $this->select_query;
    }

    public function setResultSetPrototype(ResultSetInterface $prototype)
    {
        $this->result_set_prototype = $prototype;
        return $this;
    }

    public function getResultSetPrototype()
    {
        if (is_null($this->result_set_prototype)) {
            $this->result_set_prototype = new ResultSet();
        }
        return $this->result_set_prototype;
    }

}
