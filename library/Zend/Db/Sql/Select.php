<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Adapter\Platform\Sql92,
    Zend\Db\Adapter\ParameterContainer;


/**
 * @property Where $where
 * @todo http://pastie.org/private/oua4j2p3bvueoyw19nefw
 */
class Select implements SqlInterface, PreparableSqlInterface
{
    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const SQL_WILDCARD = '*';

    protected $specification1 = 'SELECT %1$s FROM %2$s';
    protected $specification2 = '%1$s JOIN %2$s ON %3$s';
    protected $specification3 = 'ORDER BY %1$s';
    protected $specification4 = 'FETCH %1$s';

    protected $table = null;
    protected $joins = array();
    protected $databaseOrSchema = null;
    protected $columns = array('*');
    protected $where = null;
    protected $order = null;
    protected $limit = null;

    public function __construct($table = null, $databaseOrSchema = null)
    {
        if ($table) {
            $this->from($table, $databaseOrSchema);
        }
        $this->where = new Where;
    }

    public function from($table, $databaseOrSchema = null)
    {
        $this->table = $table;
        $this->databaseOrSchema = $databaseOrSchema;
        return $this;
    }

    public function columns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /*
    public function union($select = array(), $type = self::SQL_UNION)
    {

    }
    */

    public function join($name, $on, $columns = self::SQL_WILDCARD, $type = self::JOIN_INNER)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->joins[] = array($name, $on, $columns, $type);
        return $this;
    }

    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND)
    {
        if ($predicate instanceof Where) {
            $this->where = $predicate;
        } elseif ($predicate instanceof \Closure) {
            $predicate($this->where);
        } else {
            if (is_string($predicate)) {
                $predicate = new Predicate\Literal($predicate);
            } elseif (is_array($predicate)) {
                foreach ($predicate as $pkey => $pvalue) {
                    if (is_string($pkey) && strpos($pkey, '?') !== false) {
                        $predicate = new Predicate\Literal($pkey, $pvalue);
                    } elseif (is_string($pkey)) {
                        $predicate = new Predicate\Operator($pkey, Predicate\Operator::OP_EQ, $pvalue);
                    } else {
                        $predicate = new Predicate\Literal($pvalue);
                    }
                }
            }
            $this->where->addPredicate($predicate, $combination);
        }
        return $this;
    }

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Adapter\Driver\StatementInterface $statement
     */
    public function prepareStatement(Adapter $adapter, StatementInterface $statement)
    {
        $platform = $adapter->getPlatform();
        $separator = $platform->getIdentifierSeparator();

        $columns = array();
        foreach ($this->columns as $columnKey => $column) {
            $columns[] = $platform->quoteIdentifierInFragment($column);

            /* if (is_int($columnKey)) {
                $columns = $platform->quoteIdentifierInFragment($column);
            } else {
                $columns = $platform->quoteIdentifierInFragment($column);
            } */

        }

        $table = $platform->quoteIdentifier($this->table);
        if ($this->databaseOrSchema != '') {
            $dbSchema = $platform->quoteIdentifier($this->databaseOrSchema) . $platform->getIdentifierSeparator();
            $table = $dbSchema . $table;
        } else {
            $dbSchema = '';
        }

        if ($this->joins) {
            $jArgs = array();
            foreach ($this->joins as $j => $join) {
                $jArgs[$j] = array();
                $jArgs[$j][] = strtoupper($join[3]); // type
                $jArgs[$j][] = $platform->quoteIdentifier($join[0]); // table
                $jArgs[$j][] = $platform->quoteIdentifierInFragment($join[1], array('=', 'AND', 'OR', '(', ')')); // on
                foreach ($join[2] as /* $jColumnKey => */ $jColumn) {
                    $columns[] = $jArgs[$j][1] . $separator . $platform->quoteIdentifierInFragment($jColumn);
                }
            }
        }


        $columns = implode(', ', $columns);

        $sql = sprintf($this->specification1, $columns, $table);

        if (isset($jArgs)) {
            foreach ($jArgs as $jArg) {
                $sql .= ' ' . vsprintf($this->specification2, $jArg);
            }
        }

        if ($this->where->count() > 0) {
            $statement->setSql($sql);
            $this->where->prepareStatement($adapter, $statement);
            $sql = $statement->getSql();
        }

        $order = null; // @todo
        $limit = null; // @todo

        $sql .= (isset($order)) ? sprintf($this->specification3, $order) : '';
        $sql .= (isset($limit)) ? sprintf($this->specification4, $limit) : '';

        $statement->setSql($sql);
    }

    public function getSqlString(PlatformInterface $platform = null)
    {
        // get platform, or create default
        $platform = ($platform) ?: new Sql92;

        // get identifier separator
        $separator = $platform->getIdentifierSeparator();

        // process column names (@todo currently simple array only)
        $columns = array();
        foreach ($this->columns as $columnKey => $column) {
            $columns[] = $platform->quoteIdentifierInFragment($column);
        }

        // process the schema and table name
        $table = $platform->quoteIdentifier($this->table);
        if ($this->databaseOrSchema != '') {
            $dbSchema = $platform->quoteIdentifier($this->databaseOrSchema) . $platform->getIdentifierSeparator();
            $table = $dbSchema . $table;
        } else {
            $dbSchema = '';
        }

        // process any joins
        if ($this->joins) {
            $jArgs = array();
            foreach ($this->joins as $j => $join) {
                $jArgs[$j] = array();
                $jArgs[$j][] = strtoupper($join[3]); // type
                $jArgs[$j][] = $platform->quoteIdentifier($join[0]); // table
                $jArgs[$j][] = $platform->quoteIdentifierInFragment($join[1], array('=', 'AND', 'OR', '(', ')')); // on
                foreach ($join[2] as $jColumnKey => $jColumn) {
                    $columns[] = $jArgs[$j][1] . $separator . $platform->quoteIdentifierInFragment($jColumn);
                }
            }
        }

        // convert columns to string
        $columns = implode(', ', $columns);

        // create sql
        $sql = sprintf($this->specification1, $columns, $table);

        // add in joins
        if (isset($jArgs)) {
            foreach ($jArgs as $jArg) {
                $sql .= ' ' . vsprintf($this->specification2, $jArg);
            }
        }

        // add in where if it exists
        if ($this->where->count() > 0) {
            $sql .= $this->where->getSqlString($platform);
        }

        // process order & limit (@todo this is too basic, but good for now)
        $sql .= (isset($this->order)) ? sprintf($this->specification3, $this->order) : '';
        $sql .= (isset($this->limit)) ? sprintf($this->specification3, $this->limit) : '';
        return $sql;
    }

    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'where':
                return $this->where;
        }
    }

}
