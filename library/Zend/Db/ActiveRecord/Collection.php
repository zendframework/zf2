<?php

namespace Zend\Db\ActiveRecord;

class Collection extends \ArrayObject
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

	/**
	 * Class used when creating an iterator.
	 *
	 * @var string
	 */
	protected $_iteratorClass = "\\Zend\\Db\\ActiveRecord\\CollectionIterator";


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
	public function __construct($data = array(), $className = null, $lazyInit = true, $iteratorClass = null){
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

		if($iteratorClass !== null){
			if(!is_subclass_of($iteratorClass,"\\Zend\\Db\\ActiveRecord\\CollectionIterator")){
				throw new Exception\Exception(get_called_class().' can work with iterators that are ActiveRecord\CollectionIterator subclasses.');
			}
			$this->_iteratorClass = $iteratorClass;
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

		return parent::__construct($inject,\ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * Sort the collection by object's name
	 *
	 * @param	string	$dir	(optional) Direction of sort, ASC|DESC
	 * @return DbObject_Collection
	 */
	public function sortByName($dir = 'ASC'){
		return $this->sortBy('name',$dir);
	}
	
	public function filter($var,$value){
		$result = new self();
		foreach($this as $e){
			if($e->$var == $value)
				$result->push($e);
		}
		return $result;
	}
	
	/**
	 * Sorts the collection by object attributes. It is possible to sort by flat attributes and
	 * sub-object attributes. Examples:
	 * 		->sortByData('name')                   :sort by name ascending
	 * 		->sortByData('price','DESC')           :sort by price descending
	 * 		->sortByData(array('parent','name'))   :sort by parent's name ascending
	 *
	 * @param 	string|array	$atr1	Mandatory, first attribute to sort with (or array of sub-object and it's attribute name)
	 * @param	string		$atr1Mode	(optional) Direction of sort, ASC|DESC (default: ASC)
	 * @param 	string|array	$atr2	(optional)
	 * @param	string		$atr2Mode	(optional) 
	 * @param 	string|array	$atr3	(optional) 
	 * @param	string		$atr1Mode	(optional) 
	 * @return DbObject_Collection
	 */
	public function sortBy($atr1,$atr1Mode='ASC',$atr2=null,$atr2Mode = 'ASC',$atr3=null,$atr3Mode='ASC'){
		if(!$this->count()){
			return $this;
		}else{
			$this->_selfSortOptions = array();
			$this->_selfSortOptions[] = array($atr1,($atr1Mode!='ASC'?'DESC':'ASC'));
			if($atr2){
				$this->_selfSortOptions[] = array($atr2,($atr2Mode!='ASC'?'DESC':'ASC'));
			}
			if($atr3){
			   $this->_selfSortOptions[] = array($atr3,($atr3Mode!='ASC'?'DESC':'ASC'));
			}
			$array = $this->getArrayCopy();
			usort($array,array($this,'_selfSort__callback'));
			$this->exchangeArray($array);
			return $this;
		}
	}
	
	protected function _selfSort__callback($a,$b){
		$x = 0;
		do {
			$atr = $this->_selfSortOptions[$x][0];
			if(is_array($atr)){
				$stackA = array();
				$stackB = array();
				$stackA[] = $a->$atr[0];
				$stackB[] = $b->$atr[0];
				for($y=1;$y<=count($atr);$y++){
					if(is_object($stackA[count($stackA)-1]) && is_object($stackB[count($stackB)-1])){
						$stackA[] = $stackA[count($stackA)-1] -> {$atr[$y]};
						$stackB[] = $stackB[count($stackB)-1] -> {$atr[$y]};
					}
				}
				$val = strcmp(array_pop($stackA),array_pop($stackB));
			}else{
				$val = strcmp($a->{$atr},$b->{$atr});
			}
			if($val){
				if($this->_selfSortOptions[$x][1] == 'ASC' && $val >= 1)
					return 1;
				elseif($this->_selfSortOptions[$x][1] == 'ASC' && $val <= -1)
					return -1;
				elseif($val == 0)
					return 0;
				elseif($this->_selfSortOptions[$x][1] == 'DESC' && $val >= 1)
					return -1;
				elseif($this->_selfSortOptions[$x][1] == 'DESC' && $val <= -1)
					return 1;
				else
					return 0;
			}
		}while(!empty($this->_selfSortOptions[++$x]));
		
		return 0;
   }
	
	public function hasObjectId($id){
		foreach($this as $obj){
			if($obj->id == $id)
				return true;
		}
		
		return false;
	}
	
	public function getObjectById($id){
		foreach($this as $obj){
			if($obj->id == $id)
				return $obj;
		}
		
		return false;
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
	
	public function has($val,$strict = true){
		if(!is_object($val) || !($val instanceof ActiveRecordAbstract))
			throw new Exception('ActiveRecordAbstract_Collection can only store ActiveRecordAbstracts');
		
		return parent::has($val,true);
	}
	
	public function unshift($value){
		if(!is_object($value) || !($value instanceof ActiveRecordAbstract))
			throw new Exception('ActiveRecordAbstract_Collection can only store ActiveRecordAbstracts');

		return parent::unshift($value);
	}
	
	
	public function __set($index,$val){
		if(!is_object($val) || !($val instanceof ActiveRecordAbstract))
			throw new Exception('ActiveRecordAbstract_Collection can only store ActiveRecordAbstracts');
			
		if($this->has($val))
			return $val;
		
		return parent::__set($index,$val);
	}
	
	public function toArray($recursive = false){
		$result = array();
		foreach($this as $obj){
			$result[] = $obj->toArray($recursive);
		}
		return $result;
	}
	

	public function getCol($key){
		$result = array();
		foreach($this as $obj){
			$result[] = $obj->$key;
		}
		return $result;
	}
	
	public function toJson(){
		return \Zend\Json\Json::encode($this->toArray());
	}

	public function getIterator(){
		return new $this->_iteratorClass($this->getArrayCopy(), $this->_className, $this->_lazyInit);
	}
	
}