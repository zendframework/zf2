<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Driver\Mysqli;

use Zend\Db\Adapter\Driver\ResultInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Result implements \Iterator, ResultInterface
{
    const MODE_STATEMENT = 'statement';
    const MODE_RESULT = 'result';
    
    /**
     * Mode
     * 
     * @var string
     */
    protected $mode = null;

    /**
     * Is query result
     * 
     * @var boolean 
     */
    protected $isQueryResult = true;

    /**
     * @var \mysqli_result|\mysqli_stmt
     */
    protected $resource = null;

    /**
     * @var boolean
     */
    protected $isResultStored = false;
    
    /**
     * Cursor position
     * @var int
     */
    protected $position = 0;

    /**
     * Number of known rows
     * @var int
     */
    protected $numberOfRows = 0;

    /**
     * Dataset has been looped, or store_result() has been called
     * @var boolean
     */
    protected $isNumberOfRowsFinal = false;
    
    /**
     *
     * @var null|false|array
     */
    protected $currentData = null;
    
    /**
     * Stores data not available anumore on mysqli
     * @var array
     */
    protected $dataBuffer = array();
    
    /**
     *
     * @var array
     */
    protected $statementBindValues = array('keys' => null, 'values' => array());

    /**
     * @var mixed
     */
    protected $generatedValue = null;

    /**
     * Initialize
     * 
     * @param  mixed $resource
     * @return Result 
     */
    public function initialize($resource, $generatedValue)
    {
        if (!$resource instanceof \mysqli && !$resource instanceof \mysqli_result && !$resource instanceof \mysqli_stmt) {
            throw new \InvalidArgumentException('Invalid resource provided.');
        }

        $this->isQueryResult = (!$resource instanceof \mysqli);

        $this->resource = $resource;
        $this->generatedValue = $generatedValue;
        $this->mode = ($this->resource instanceof \mysqli_stmt) ? self::MODE_STATEMENT : self::MODE_RESULT;
        return $this;
    }
    
    /**
     *
     * @return mixed 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set is query result
     * 
     * @param boolean $isQueryResult 
     */
    public function setIsQueryResult($isQueryResult)
    {
        $this->isQueryResult = $isQueryResult;
    }

    /**
     * Is query result
     * 
     * @return boolean 
     */
    public function isQueryResult()
    {
        return $this->isQueryResult;
    }

    /**
     * Get affected rows
     * 
     * @return integer 
     */
    public function getAffectedRows()
    {
        if ($this->resource instanceof \mysqli || $this->resource instanceof \mysqli_stmt) {
            return $this->resource->affected_rows;
        } else {
            return $this->resource->num_rows;
        }
    }
    
    /**
     * Current
     * 
     * @return mixed 
     */
    public function current()
    {
        if (is_null($this->currentData)) {
            //Load current row
            if ($this->mode == self::MODE_STATEMENT) {
                $this->loadDataFromMysqliStatement();
            } else {
                $this->loadFromMysqliResult();
            }
        }
        
        return $this->currentData;
    }
    
    /**
     * Mysqli's binding and returning of statement values
     * 
     * Mysqli requires you to bind variables to the extension in order to 
     * get data out.  These values have to be references:
     * @see http://php.net/manual/en/mysqli-stmt.bind-result.php
     * 
     * @throws \RuntimeException
     * @return bool
     */
    protected function loadDataFromMysqliStatement()
    {

        if (isset($this->dataBuffer[$this->position])) {
            $this->currentData  =  $this->dataBuffer[$this->position];
            return $this->dataBuffer[$this->position];
        }
        
        // build the default reference based bind strutcure, if it does not already exist
        if ($this->statementBindValues['keys'] === null) {
            $this->statementBindValues['keys'] = array();
            $resultResource = $this->resource->result_metadata();
            foreach ($resultResource->fetch_fields() as $col) {
                $this->statementBindValues['keys'][] = $col->name;
            }
            $this->statementBindValues['values'] = array_fill(0, count($this->statementBindValues['keys']), null);
            $refs = array();
            foreach ($this->statementBindValues['values'] as $i => &$f) {
                $refs[$i] = &$f;
            }
            call_user_func_array(array($this->resource, 'bind_result'), $this->statementBindValues['values']);
        }
        
        if (($r = $this->resource->fetch()) === null) {
            
            $this->isNumberOfRowsFinal           = true;
            $this->currentData                   = false;
            $this->dataBuffer[$this->position]   = false;
            $this->resource->close(); //Free resources 
            return false;
            
        } elseif ($r === false) {
            throw new \RuntimeException($this->resource->error);
        }

        // dereference
        for ($i = 0; $i < count($this->statementBindValues['keys']); $i++) {
            $this->currentData[$this->statementBindValues['keys'][$i]] = $this->statementBindValues['values'][$i];
        }

        $this->dataBuffer[$this->position] = $this->currentData; 
        
        //Data buffering prevents from double counting
        if (!$this->isNumberOfRowsFinal) {
            $this->numberOfRows++;
        }
        
        return true;
    }
    
    /**
     * Load from mysqli result
     * 
     * @return boolean 
     */
    protected function loadFromMysqliResult()
    {

        if (isset($this->dataBuffer[$this->position])) {
            $this->currentData  =  $this->dataBuffer[$this->position];
            return $this->dataBuffer[$this->position];
        }
        
        if (($data = $this->resource->fetch_assoc()) === null) {
            $this->isNumberOfRowsFinal = true;
            $this->currentData         = false;
            $this->resource->free(); //Free resources
            return false;
        }
        
        $this->currentData                 = $data;
        $this->dataBuffer[$this->position] = $this->currentData;
        
        //Data buffering prevents from double counting
        if (!$this->isNumberOfRowsFinal) {
            $this->numberOfRows++;
        }
        
        return true;
    }
    
    /**
     * Next
     */
    public function next()
    {
        if (is_null($this->currentData)) {
            //Called first, or called 2+ next() in sequence. Need to advance the pointer of mysqli
            $this->current();
        }
        
        //If false do nothing, rows are finished and it cannot advance
        if (!is_null($this->currentData) && $this->currentData !== false) {
            $this->currentData = null;
            $this->position ++;
        }
 
    }
    
    /**
     * Key
     * 
     * @return mixed 
     */
    public function key()
    {
        return $this->position;
    }
    
    /**
     * Rewind
     * 
     */
    public function rewind()
    {
        if ($this->position == 0)
            return;
        
        /* 
        //Buffering makes this useless, and it didn't work unless results are stored as first step!
        if ($this->resource instanceof \mysqli_stmt && !$this->isResultStored) {
            $this->resource->store_result(); //Required for data_seek to work
            $this->isResultStored = true;
        }    
        
        //Works with \mysqli_result only if the results are buffered
        $this->resource->data_seek(0);

        */
        
        $this->position = 0;
        $this->currentData = null;
    }
    
    /**
     * Valid
     * 
     * @return boolean 
     */
    public function valid()
    {
        if (is_null($this->currentData))
            $this->current();
        
        return !empty($this->currentData);
        
    }
    
    /**
     * Count
     * 
     * @return integer 
     */
    public function count()
    {
        //If results are not stored, number of rows is zero
        if (!$this->isNumberOfRowsFinal) {
            if ($this->resource instanceof \mysqli_stmt && !$this->isResultStored) {
                $this->resource->store_result();
                $this->isResultStored = true;
            }
            $this->numberOfRows += $this->resource->num_rows;
            $this->isNumberOfRowsFinal = true;
        }
        
        return $this->numberOfRows;
    }

    /**
     * Generated Value
     *
     * @return mixed
     */
    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }
}
