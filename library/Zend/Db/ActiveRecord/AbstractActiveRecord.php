<?php

namespace Zend\Db\ActiveRecord;
use \Zend as Zend;
use \ReflectionClass;
use \Zend\Registry as Registry;

abstract class AbstractActiveRecord
{
	// CRUD operations
	const OP_CREATE = 1;
	const OP_READ = 2;
	const OP_UPDATE = 3;
	const OP_DELETE = 4;
	const OP_META = 50;
	const OP_OTHER = 100;

	/**
	 * @var \ReflectionClass
	 */
	protected static $_class;

	/**
	 * Database table name.
	 * @var string
	 */
	protected static $_dbTable;

	/**
	 * Primary key name (id column)
	 * @var string
	 */
	protected static $_pk = 'id';

	/**
	 * Should all changes be auto saved on shutdown
	 * @var bool
	 */
	protected static $_autoSave = false;

	/**
	 * If set to true, each unique object instance (unique by ID) will be created only once, and reused throughout
	 * the lifetime of the application. There will never be two ActiveRecord objects of the same class and the same
	 * id. Use persistence when you construct the same objects at multiple points in your app (i.e. the same User object
	 * gets created in Controller while listing Users and in other ActiveRecord class as its owner)
	 *
	 * @var bool	Defaults to true
	 */
	protected static $_persistent = true;

	/**
	 * Should record data be saved to cache
	 * @var bool
	 */
	protected static $_cacheEnabled = false;

	/**
	 * @var Zend\Cache\Frontend\Core
	 */
	protected $_cache;

	/**
	 * @var Zend\Cache\Frontend\Core
	 */
	private static $_globalDefaultCache;

	/**
	 * Subclass default db adapter
	 *
	 * @var Zend\Cache\Frontend\Core
	 */
	protected static $_defaultCache;

	/**
	 * Global ActiveRecord default db adapter
	 *
	 * @var Zend\Db\Adapter\AbstractAdapter
	 */
	private static $_globalDefaultDb;

	/**
	 * Subclass default db adapter
	 *
	 * @var Zend\Db\Adapter\AbstractAdapter
	 */
	protected static $_defaultDb;

	/**
	 * DbTable columns
	 *
	 * @var array
	 */
	protected static $_columns;

	/**
	 * Current db adapter for all operations.
	 *
	 * @var Zend\Db\Adapter\AbstractAdapter|string		Adapter object or a key in Zend\Registry to fetch it.
	 */
	protected $_db;

	/**
	 * Current instance id
	 *
	 * @var int
	 */
	protected $_id;

	/**
	 * Current instance data
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * List of all changed properties
	 *
	 * @var array
	 */
	protected $_changedData = array();

	/**
	 * Has this record been loaded?
	 *
	 * @var bool
	 */
	protected $_loaded = false;



	public function __construct($params = null, $instanceId = null, \Zend\Db\Adapter\AbstractAdapter $dbObj = null ){
		//if(!static::$_class $this->_class = new ReflectionClass(get_class($this));

		if($instanceId !== null){
			$this->_id = $instanceId;
			
			if(is_array($params))
				$this->_data = $params;

			if(is_object($dbObj))
				$this->_db = $dbObj;
		}else{
			if(is_array($params)){
				if(static::$_columns === null)
					$this->_loadColumns();

				foreach($params as $key=>$val){
					$this->__set($key,$val);
				}
			}
		}



		/*if(!empty($params['db']))
			$this->setDbAdapter($params['db']);
		elseif(!empty($params['dbAdapter']))
			$this->setDbAdapter($params['dbAdapter']);
		else
			$this->setDbAdapter();

		if(!empty($params['cache']))
			$this->_setCache($params['cache']);
		elseif(static::$_cacheEnabled)
			$this->_setCache();

		// determine dbtable if necessary
		if(!static::$_dbTable)
			static::$_dbTable = $this->_determineDbTable();

		*/
	}


	/**
	 * Get the db adapter. Override this function to implement dynamic database adapter selection (for clustering,
	 * sharding, distributed, master-slave and other database configurations)
	 * @throws Exception
	 * @param int $operation					Currently pending operation, one of the AbstractActiveRecord::OP_* constants
	 * @return string|\Zend\Db\Adapter\AbstractAdapter
	 */
	protected function _getDb($operation = self::OP_OTHER){
		if(isset($this)){
			if(is_object($this->_db))
				return $this->_db;

			if($this->_db){
				// try to load db adapter from Zend\Registry
				$dbObj = \Zend\Registry::get($this->_db);
				if(is_object($dbObj) && $dbObj instanceof Zend\Db\Adapter\AbstractAdapter){
					$this->_db = $dbObj;
				}else{
					throw new Exception\Exception('Cannot find db adapter in Zend\Registry using name "'.$this->_db.'"');
				}
			}else{
				// use subclass default db
				if(static::$_defaultDb){
					$this->_db = static::$_defaultDb;
				}

				// use global ActiveRecord default db
				elseif(self::$_globalDefaultDb){
					$this->_db = self::$_globalDefaultDb;
				}

				// try to fetch default Db\Table\AbstractTable adapter
				elseif($db = Zend\Db\Table\Table::getDefaultAdapter()){
					$this->_db = $db;
				}

				// we cannot go on without db adapter
				else{
					throw new Exception\Exception('Cannot find any db adapter for '.get_called_class());
				}
			}

			return $this->_db;
		}else{
			// static call

			// use subclass default db
			if(static::$_defaultDb){
				return static::$_defaultDb;
			}

			// use global ActiveRecord default db
			elseif(self::$_globalDefaultDb){
				return self::$_globalDefaultDb;
			}

			// try to fetch default Db\Table\AbstractTable adapter
			elseif($db = Zend\Db\Table\Table::getDefaultAdapter()){
				return $db;
			}

			// we cannot go on without db adapter
			else{
				throw new Exception\Exception('Cannot find any db adapter for '.get_called_class());
			}
		}
	}

	/**
	 * Return cache frontend. Override this function to implement dynamic cache selection (for example for 2-level,
	 * 3-level caching, cache sharding, disabling cache for some CRUD operations, etc.)
	 * Warning! This function is called statically and dynamically.
	 *
	 * @throws Exception
	 * @param int $operation
	 * @return bool|\Zend\Cache\Frontend\Core
	 */
	protected function _getCache($operation = self::OP_OTHER){
		if(isset($this)){
			if(!static::$_cacheEnabled || $this->_cache === false)
				return false;
			elseif(is_object($this->_cache))
				return $this->_cache;

			if($this->_cache){
				// try to load db adapter from Zend\Registry
				$cacheObj = \Zend\Registry::get($this->_cache);
				if(is_object($cacheObj) && $cacheObj instanceof Zend\Cache\Frontend\Core){
					$this->_cache = $cacheObj;
				}else{
					throw new Exception\Exception('Cannot find cache frontend in Zend\Registry using name "'.$this->_cache.'"');
				}
			}else{
				// use subclass default cache
				if(static::$_defaultCache){
					$this->_cache = static::$_defaultCache;
				}

				// use global ActiveRecord default cache
				elseif(self::$_globalDefaultCache){
					$this->_cache = self::$_globalDefaultCache;
				}

				// do not use cache
				else{
					$this->_cache = false;
				}
			}

			return $this->_cache;
		}else{
			// static call
			if(static::$_defaultCache){
				return static::$_defaultCache;
			}

			// use global ActiveRecord default cache
			elseif(self::$_globalDefaultCache){
				return self::$_globalDefaultCache;
			}

			// do not use cache
			else{
				return false;
			}
		}
	}

	/**
	 * Save changes to database and cache (if enabled)
	 * 
	 * @throws Exception
	 * @param bool $force		If true, force update row with all in-memory data regardless if anything was changed.
	 * @return AbstractActiveRecord
	 */
	public function save($force = false){
		if($force){
			// save all stored data to db
			$changeData = $this->_data;
		}elseif(count($this->_changedData)){
			$changeData = array();
			foreach($this->_changedData as $k=>$foo){
				$changeData[$k] = $this->_data[$k];
			}
		}else{
			// nothing to do
			return $this;
		}

		if($this->_id){
			// save changes to DB
			$db = $this->_getDb(self::OP_UPDATE);
			$db->update(
				static::$_dbTable,
				$changeData,
				$db->quoteInto(self::$_pk.' = ?',$this->_id)
			);
		}else{
			// create new DB row
			$db = $this->_getDb(self::OP_CREATE);
			$db->insert(
				static::$_dbTable,
				$changeData
			);

			// retrieve object id
			if(!($this->_id = $db->lastInsertId(static::$_dbTable)))
				throw new Exception\Exception('Cannot retrieve new record id for table "'.static::$_dbTable.'" (is the primary key set as AUTO_INCREMENT, IDENTITY or has a SEQUENCE?)');
			
		}

		// save changes to cache (if enabled)
		$this->_cacheUpdate();

		$this->_changedData = array();

		return $this;
	}

	/**
	 * Load record data from database
	 *
	 * @param bool $force
	 * @throws Exception|NotFoundException
	 * @return void
	 */
	public function _loadFromDb($force = false){
		if(!$this->_id)
			throw new Exception\Exception('Cannot load object from DB, because we dont know object ID.');
		elseif($this->_loaded && !$force)
			return;

		// determine db table name (if not predefined)
		if(!static::$_dbTable)
			static::$_dbTable = static::_determineDbTable();


		// load row from db
		$db = $this->_getDb(self::OP_READ);
		$select = $db->select()->from(static::$_dbTable)->where($this->_fk.' = ?',$this->_id)->limit(1);
		$data = $select->query(\Zend\Db\Db::FETCH_ASSOC);
		if(!count($data)){
			throw new Exception\NotFoundException($this->_id,get_class($this));
		}

		$this->_data = $data[0];

		// populate table columns
		if(static::$_columns === null){
			static::$_columns = array_keys($this->_data);
		}
	}


	/**
	 * Save updated object data to cache. Override this function to implement custom cache handling.
	 * 
	 * @return void
	 */
	protected function _cacheUpdate(){
		if($cache = $this->_getCache(self::OP_UPDATE,$this)){
			$cache->save($this->_data,get_class($this).'_'.$this->_id);
		}
	}

	/**
	 * Load columns (properties) info from cache or database.
	 *
	 * @throws Exception
	 * @return array
	 */
	protected function _loadColumns(){
		// load columns from cache
		if(static::_cacheLoadColumns()){
			return static::$_columns;
		}

		// determine db table name (if not predefined)
		if(!static::$_dbTable)
			static::$_dbTable = static::_determineDbTable();

		// load columns from db
		$db = $this->_getDb(self::OP_META);
		$meta = $db->describeTable(static::$_dbTable);
		if(!count($meta))
			throw new Exception\Exception('Cannot determine table "'.static::$_dbTable.'" columns');

		static::$_columns = array();
		foreach($meta as $column => $props){
			static::$_columns[$column] = true;
		}
		static::_cacheUpdateColumns();

		return static::$_columns;
	}

	/**
	 * ---=---=---=---=---=---=---=---=---=   STATIC METHODS BELOW ---=---=---=---=---=---=---=---=---=---=---=---
	 */

	 /**
	  * Return new ActiveRecord instance. If supplied a numeric value or a string, an ActiveRecord with that id will
	  * be instantiated but not loaded. If supplied an array, a new instance will be created and a new record will be
	  * inserted into DB after save().
	  *
	  * @static
	  * @throws
	  * @param array $param		ActiveRecord id or an array of values
	  * @return AbstractActiveRecord
	  */
	public static function factory($param = null){
	 	if(is_scalar($param)){
	 		// try to find the object in Registry
	 		if(
				static::$_persistent &&
				($obj = Registry::get(get_called_class().'_'.$param))
			){
				return $obj;
			}

			// create new instance but don't load it from db yet
	 		$obj = new static(null,$param);

	 		// store it in registry
	 		if(static::$_persistent)
	 			Registry::set(get_called_class().'_'.$param,$obj);

	 		return $obj;
	 	}elseif(is_array($param)){
	 		// create completely new object with the passed data
	 		return new static($param);
	 	}
	 	
	 	else{
	 		throw new Exception\BadMethodCallException('Cannot call '.get_called_class().'::factory() with a parameter of type '.gettype($param));
	 	}
	 }



	/**
	 * Return ActiveRecord instance with the supplied id. If current class is persistent, try to find it in Registry,
	 * otherwise load from cache or db. The resulting object will already be loaded.
	 *
	 * @static
	 * @throws BadMethodCallException
	 * @param $id
	 * @return AbstractActiveRecord|false		An object instance or false if could not find it.
	 */
	public static function findById($id){
		if(!is_scalar($id))
			throw new Exception\BadMethodCallException('Cannot call '.get_called_class().'::findById() with parameter of type '.gettype($id));

		// search for the object in the Registry
		if(
			static::$_persistent &&
			($obj = Registry::get(get_called_class().'_'.$id))
		){
			return $obj;
		}

		// try to load from cache
		if(
			static::$_cacheEnabled &&
			($cache = static::_getCache(self::OP_READ)) &&
			is_array($data = $cache->load(get_called_class().'_'.$id))
		){
			// create new object instance and inject its id and data
			$obj = new static($data,$id);

			// store it in registry
			if(static::$_persistent)
				Registry::set(get_called_class().'_'.$id,$obj);

			return $obj;
		}
		

		// determine db table name (if not predefined)
		if(!static::$_dbTable)
			static::$_dbTable = static::_determineDbTable();

		// load from db
		$db = static::_getDb(self::OP_READ);
		$result = $db->select()
					->from(static::$_dbTable)
					->where($db->quoteIdentifier(static::$_fk).' = ?',$id)
					->limit(1)
					->query(\Zend\Db\Db::FETCH_ASSOC)
		;
		if(!count($result))
			return false;

		// create new object instance and inject its id and data
		$obj = new static($result[0],$id);

		// store it in registry
		if(static::$_persistent)
			Registry::set(get_called_class().'_'.$id,$obj);

		// store it in cache
		if(
			static::$_cacheEnabled &&
			($cache = static::_getCache(self::OP_UPDATE))
		){
			static::_cacheStore($result[0],$id);
		}

		return $obj;
	}

	/**
	 * If supplied with a scalar, will search for ActiveRecord with that id.
	 * If supplied with array, will perform a db search for ActiveRecords matching those params.
	 *
	 * @static
	 * @throws BadMethodCallException
	 * @param array|string|integer 						$params			Id (scalar) to find one object or search parameters (array)
	 * @param array 									$options		Options for findAll (sort, limit, etc.)
	 * @return AbstractActiveRecord|Collection|false
	 */
	public static function find($params = array(),$options = array()){
		if(is_scalar($params)){
			return static::findById($params);
		}elseif(is_array($params))
			return static::findAll($params,$options);
		else
			throw new Exception\BadMethodCallException('Cannot call '.get_called_class().'::find() with first param of type '.gettype($params));
	}

	public static function findAll($params = array(),$options = array()){
		
	}

	public static function findOne($params = array(), $options = array()){
		$options['limit'] = 1;
		$result = static::findAll($params,$options);
		return $result->first();
	}

	/**
	 * Load column names from cache
	 *
	 * @return array|bool
	 */
	protected function _cacheLoadColumns(){
		if($cache = $this->_getCache(self::OP_META)){
			if(is_array($columns = $cache->load(get_called_class().'_columns'))){
				static::$_columns = $columns;
				return static::$_columns;
			}
		}
		return false;
	}

	/**
	 * Save column information to cache
	 *
	 * @return bool
	 */
	protected function _cacheUpdateColumns(){
		if($cache = $this->_getCache(self::OP_META)){
			return $cache->save(static::$_columns,get_called_class().'_columns');
		}
		return false;
	}

	/**
	 * Determine the db table name to use for this active record. Override this function to implement custom table
	 * name generation (inflection).
	 *
	 * @return string
	 */
	protected static function _determineDbTable(){
		$dbTable = strtolower(get_called_class());					// get current class name
		$dbTable = substr($dbTable,strripos($dbTable,'\\')+1);		// strip namespace
		return $dbTable;
	}


	protected function _cacheStore($data,$id){
		if($cache = static::_getCache(self::OP_CREATE)){
			$cache->save($data,get_called_class().'_'.$id);
		}
	}

	/**
	 * Store the default db adapter to use for all active record instances
	 * @static
	 * @param null|\Zend\Db\Adapter\AbstractAdapter $db
	 * @return void
	 */
	public static function setDefaultDb(\Zend\Db\Adapter\AbstractAdapter $db = null){
		if(get_called_class() === __CLASS__)
			self::$_globalDefaultDb = $db;
		else
			static::$_defaultDb = $db;
	}

	/**
	 * Set the default db adapter to use for all active record instances
	 * @static
	 * @param null|\Zend\Db\Adapter\AbstractAdapter $db
	 * @return void
	 */
	public static function setDefaultAdapter(Zend\Db\Adapter\AbstractAdapter $db = null){
		self::setDefaultDb($db);
	}

	/**
	 * @static
	 * @return Zend\Cache\Frontend\Core
	 */
	public static function getDefaultDb(){
		if(get_called_class() !== __CLASS__ && static::$_defaultDb)
			return static::$_defaultDb;
		else
			return self::$_globalDefaultDb;
	}

	/**
	 * @static
	 * @return Zend\Cache\Frontend\Core
	 */
	public static function getDefaultAdapter(){
		if(get_called_class() !== __CLASS__ && static::$_defaultDb)
			return static::$_defaultDb;
		else
			return self::$_globalDefaultDb;
	}

	/**
	 * Set the default cache frontend to use when caching record data
	 *
	 * @static
	 * @param Zend\Cache\Frontend\Core $cache
	 * @return void
	 */
	public static function setDefaultCache(Zend\Cache\Frontend\Core $cache = null){
		if(get_called_class() === __CLASS__)
			self::$_globalDefaultCache = $cache;
		else
			static::$_defaultCache = $cache;
	}

	/**
	 * @static
	 * @return Zend\Cache\Frontend\Core
	 */
	public static function getDefaultCache(){
		if(get_called_class() !== __CLASS__ && static::$_defaultCache)
			return static::$_defaultCache;
		else
			return self::$_globalDefaultCache;
	}

	public function __get($prop){
		// return object id (performance)
		if($prop == 'id' || $prop == static::$_pk)
			return $this->_id;

		// call custom method
		elseif(method_exists($this,'get'.$prop))
			return call_user_func(array($this, 'get' . $prop));

		if($this->_id){
			// we have object id, load data from db
			if(!$this->_loaded)
				$this->_loadFromDb();

			if(isset($this->_data[$prop]))
				return $this->_data[$prop];
		}else{
			// return in-memory property value
			if(isset($this->_data[$prop]))
				return $this->_data[$prop];
		}

		throw new Exception\UndefinedPropertyException(get_class($this),$prop);
	}

	public function __set($prop,$val){
		// return object id (performance)
		if($prop == 'id' || $prop == static::$_pk)
			return $this->_id = $val;

		// call custom method
		elseif(method_exists($this,'set'.$prop))
			return call_user_func(array($this, 'set' . $prop));

		if($this->_id){
			if(!$this->_loaded){
				// we have object id, load data from db
				$this->_loadFromDb();
			}

			if(isset($this->_data[$prop])){
				return $this->_data[$prop] = $val;
			}else{
				throw new Exception\UndefinedPropertyException(get_class($this),$prop);
			}

		}else{
			// this is a completely new object. Make sure we know column names
			$this->_loadColumns();

			// check if column (property) exists before setting it
			if(isset(static::$_columns[$prop])){
				return $this->_data[$prop] = $val;
			}else{
				throw new Exception\UndefinedPropertyException(get_class($this),$prop);
			}
		}
	}

	public static function __callStatic($name,$args){
		if(substr($name,0,6) == 'findBy'){
			$what = substr($name,6);
			return static::findAll(array($what => $args));
		}elseif(substr($name,0,9) == 'findOneBy'){
			$what = substr($name,9);
			return static::findOne(array($what => $args));
		}
	}


}


