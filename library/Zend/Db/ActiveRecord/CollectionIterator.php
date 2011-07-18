<?php

namespace Zend\Db\ActiveRecord;


class CollectionIterator extends \ArrayIterator
{
	/**
	 * Class name that will be used when creating object instances.
	 *
	 * @var string
	 */
	protected $_className;

	/**
	 * Should ActiveRecord objects be initialized only on fetch?
	 *
	 * @var bool
	 */
	protected $_lazyInit = true;


	protected $_selfSortOptions = array();
	public $total;

	/**
	 * @param array $data				Data to inject in the form of "array of objects" or "array of arrays":
	 * 										$data = array( ActiveRecord, ActiveRecord, ... );
	 * 										$data = array(
	 * 											array( 'prop' => 'data, 'prop' => 'data' , ...)
	 * 										);
	 *
	 * @param string $className 		Name of the class that will be held inside collection
	 * @param bool   $lazyInit			True if objects be instantiated only when retrieved.
	 */
	public function __construct($data = array(), $className = null, $lazyInit = true){
		$this->_className = $className;

		if($lazyInit !== null){
			$this->_lazyInit = $lazyInit;
		}

		if($className !== null){
			if(!is_subclass_of($className,'\Zend\Db\ActiveRecord\AbstractActiveRecord')){
				throw new Exception\Exception(get_called_class().' will only work with AbstractActiveRecord subclasses.');
			}
			
			$this->_className = $className;
		}elseif($this->_lazyInit){
			throw new Exception\Exception('Cannot construct '.get_called_class().' with lazy init and no class name');
		}

		$inject = array();

		if(!$this->_lazyInit){
			// make sure each entry is an object
			foreach($data as $d){
				if(!is_object($d)){
					if(!$this->_className){
						throw new Exception\Exception(
							'Cannot construct '.get_called_class().' because one of the entries is not an object '.
							'and no class name has been supplied.'
						);
					}
					$inject[] = new $this->_className($d);
				}
			}
		}else{
			$inject = $data;
		}

		return parent::__construct($inject);
	}


	public function offsetSet($index,$val){
		if(!$this->_lazyInit && !is_object($val) || !($val instanceof ActiveRecordAbstract)){
			throw new Exception('ActiveRecordAbstract_Collection can only store ActiveRecordAbstract objects, '.gettype($val).(is_object($val)?' '.get_class($val):'').' given');
		}elseif(!$this->_lazyInit && is_object($val) && !($val instanceof $this->_className)){
			throw new Exception('This Collection can only store objects of class '.$this->_className.', '.gettype($val).(is_object($val)?' '.get_class($val):'').' given');
		}elseif($this->_lazyInit && !is_object($val) && !is_array($val)){
			throw new Exception('Object or Array expected - '.gettype($val).(is_object($val)?' '.get_class($val):'').' given');
		}
		return parent::offsetSet($index,$val);
	}

    /**
	 * @param 	integer		$index
	 * @return	\Zend\Db\ActiveRecord\AbstractActiveRecord|null
	 */
    public function offsetGet($index){
    	if(!$this->_lazyInit){
    		return parent::offsetGet($index);
    	}else{
    		$data = parent::offsetGet($index);
    		if(is_array($data)){
    			$obj = new $this->_className($data);
    			parent::offsetSet($index,$obj);
    			return $obj;
			}else{
				return $data;
			}
		}
    }

    /**
	 * @param $val
	 * @return void
	 */
    public function append($val){
		return static::offsetSet(null,$val);
    }

    public function current(){
    	if(!$this->_lazyInit){
    		return parent::current();
    	}else{
    		$data = parent::current();
    		if(is_array($data)){
    			$obj = new $this->_className($data);
    			parent::offsetSet(static::key(),$obj);
    			return $obj;
			}else{
				return $data;
			}
		}
    }


}