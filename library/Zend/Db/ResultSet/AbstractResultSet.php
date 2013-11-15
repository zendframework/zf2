<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\ResultSet;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use Zend\Db\Adapter\Driver\ResultInterface;

abstract class AbstractResultSet implements Iterator, ResultSetInterface
{
    /**
     * if -1, datasource is already buffered
     * if -2, implicitly disabling buffering in ResultSet
     * if false, explicitly disabled
     * if null, default state - nothing, but can buffer until iteration started
     * if array, already buffering
     * @var mixed
     */
    protected $buffer = null;

    /**
     * @var null|int
     */
    protected $count = null;

    /**
     * @var Iterator|IteratorAggregate|ResultInterface
     */
    protected $dataSource = null;

    /**
     * @var int
     */
    protected $fieldCount = null;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var mixed
     */
    protected $current = null;

    /**
     * Set the data source for the result set
     *
     * @param  Iterator|IteratorAggregate|ResultInterface $dataSource
     * @return ResultSet
     * @throws Exception\InvalidArgumentException
     */
    public function initialize($dataSource)
    {
        // reset buffering
        if (is_array($this->buffer)) {
            $this->buffer = array();
        }

        $this->count      = null;
        $this->fieldCount = null;
        $this->current    = null;

        if ($dataSource instanceof ResultInterface) {
            $this->count = $dataSource->count();
            $this->fieldCount = $dataSource->getFieldCount();
            $this->dataSource = $dataSource;
            if ($dataSource->isBuffered()) {
                $this->buffer = -1;
            }
            if (is_array($this->buffer)) {
                $this->dataSource->rewind();
            }
            return $this;
        } elseif (is_array($dataSource)) {
            // its safe to get numbers from an array
            $first = current($dataSource);
            reset($dataSource);
            $this->count = count($dataSource);
            $this->fieldCount = count($first);
            $this->dataSource = new ArrayIterator($dataSource);
            $this->buffer = -1; // array's are a natural buffer
        } elseif ($dataSource instanceof IteratorAggregate) {
            $this->dataSource = $dataSource->getIterator();
        } elseif ($dataSource instanceof Iterator) {
            $this->dataSource = $dataSource;
        } else {
            throw new Exception\InvalidArgumentException('DataSource provided is not an array, nor does it implement Iterator or IteratorAggregate');
        }

        if ($this->count === null && $this->dataSource instanceof Countable) {
            $this->count = $this->dataSource->count();
        }

        if ($this->fieldCount === null) {
            if ($this->count == 0) {
                $this->fieldCount = 0;
            } else {
                $dataSource->rewind();
                $row = $dataSource->current();
                if ($row instanceof Countable) {
                    $this->fieldCount = $row->count();
                } else {
                    $this->fieldCount = count((array)$row);
                }
            }
        } else {
            $this->fieldCount = 0;
        }

        return $this;
    }

    public function buffer()
    {
        if ($this->buffer === -2) {
            throw new Exception\RuntimeException('Buffering must be enabled before iteration is started');
        } elseif ($this->buffer === null) {
            $this->buffer = array();
            if ($this->dataSource instanceof ResultInterface) {
                $this->dataSource->rewind();
            }
        }
        return $this;
    }

    public function isBuffered()
    {
        return ($this->buffer === -1 || is_array($this->buffer));
    }

    protected function checkBuffered()
    {
        if ($this->buffer === null) {
            // implicitly disable buffering from here on
            $this->buffer = -2;
        }
        return $this;
    }

    /**
     * Get the data source used to create the result set
     *
     * @return null|Iterator
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Retrieve count of fields in individual rows of the result set
     *
     * @return int
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     * Iterator: move pointer to next item
     *
     * @return void
     */
    public function next()
    {
        $this->checkBuffered();
        $this->dataSource->next();
        $this->position++;
        $this->current = null;
    }

    /**
     * Iterator: retrieve current key
     *
     * @return mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator: get current item
     *
     * @return array
     */
    public function current()
    {
        if ($this->current !== null) {
            return $this->current;
        }
        $this->checkBuffered();
        if (is_array($this->buffer)) {
            if (!isset($this->buffer[$this->position])) {
                return $this->current = $this->buffer[$this->position] = $this->hydrateCurrent();
            }
            return $this->current = $this->buffer[$this->position];
        }
        return $this->current = $this->hydrateCurrent();
    }

    protected function hydrateCurrent()
    {
        return $this->dataSource->current();
    }

    protected function extract($data)
    {
        if (is_array($data)) {
            return $data;
        } elseif (method_exists($data, 'toArray')) {
            return $data->toArray();
        } elseif (method_exists($data, 'getArrayCopy')) {
            return $data->getArrayCopy();
        } else {
            throw new Exception\RuntimeException(
                'Rows as part of this DataSource, with type ' . gettype($data) . ' cannot be cast to an array'
            );
        }
        return $data;
    }

    /**
     * Iterator: is pointer valid?
     *
     * @return bool
     */
    public function valid()
    {
        if (is_array($this->buffer) && isset($this->buffer[$this->position])) {
            return true;
        }
        if ($this->dataSource instanceof Iterator) {
            return $this->dataSource->valid();
        } else {
            $key = key($this->dataSource);
            return ($key !== null);
        }
    }

    /**
     * Iterator: rewind
     *
     * @return void
     */
    public function rewind()
    {
        if (!is_array($this->buffer)) {
            if ($this->dataSource instanceof Iterator) {
                $this->dataSource->rewind();
            } else {
                reset($this->dataSource);
            }
        }
        $this->position = 0;
        $this->current = null;
    }

    /**
     * Countable: return count of rows
     *
     * @return int
     */
    public function count()
    {
        if ($this->count !== null) {
            return $this->count;
        }
        $this->count = count($this->dataSource);
        return $this->count;
    }

    /**
     * Cast result set to array of arrays
     *
     * @return array
     * @throws Exception\RuntimeException if any row is not castable to an array
     */
    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            $return[] = $this->extract($row);
        }
        return $return;
    }
}
