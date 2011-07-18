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
	 * Name of the class that will be used when creating collections (i.e. when retrieving multiple records from
	 * database.
	 *
	 * @var string
	 */
	protected static $_collectionClass = '\\Zend\\Db\\ActiveRecord\\Collection';

	/**
	 * @var Zend\Cache\Frontend\Core
	 */
	protected $_cache;

	/**
	 * This subclass default db adapter (leave empty to use global default)
	 * If this is a string, db adapter with that id will be fetched from Zend\Registry.
	 *
	 * @var Zend\Cache\Frontend\Core|string|null
	 */
	protected static $_defaultCache;

	/**
	 * @var Zend\Cache\Frontend\Core
	 */
	private static $_globalDefaultCache;

	/**
	 * Subclass default db adapter (array keys are class names)
	 *
	 * @var Zend\Cache\Frontend\Core
	 */
	private static $_subclassDefaultCache = array();

	/**
	 * This subclass default db adapter (leave empty to use global default).
	 * If this is a string, db adapter with that id will be fetched from Zend\Registry.
	 *
	 * @var Zend\Db\Adapter\AbstractAdapter|string|null
	 */
	protected static $_defaultDb;

	/**
	 * Global ActiveRecord default db adapter
	 *
	 * @var Zend\Db\Adapter\AbstractAdapter
	 */
	private static $_globalDefaultDb;

	/**
	 * Subclass default db adapter (array keys are class names)
	 *
	 * @var Zend\Db\Adapter\AbstractAdapter
	 */
	private static $_subclassDefaultDb = array();

	/**
	 * DbTable columns
	 *
	 * @var array
	 */
	protected static $_columns;

	/**
	 * Local cache for column names of different ActiveRecord subclasses
	 *
	 * @var array
	 */
	private static $_columnsCache = array();

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



	/**
	 * Warning! You should NOT use the default constructor - i.e. $foo = new Record();
	 * Instead use one of:   factory()    find()    findById()     findAll()
	 *
	 * @param null $params
	 * @param null $instanceId
	 * @param null|\Zend\Zend\Db\Adapter\AbstractAdapter $dbObj
	 */
	public function __construct($params = null, $instanceId = null, \Zend\Db\Adapter\AbstractAdapter $dbObj = null ){
		//if(!static::$_class $this->_class = new ReflectionClass(get_class($this));

		// check if primary key has been supplied with data
		if($instanceId === null && is_array($params) && isset($params[static::$_pk])){
			$instanceId = $params[static::$_pk];
		}

		if($instanceId !== null){
			// we are creating an instance of existing record
			$this->_id = $instanceId;
			
			if(is_array($params)){
				$this->_data = $params;
				$this->_loaded = true;
			}

			if(is_object($dbObj))
				$this->_db = $dbObj;


			// store myself in registry
			if(static::$_persistent){
				Registry::set(strtr(get_called_class(),'\\','_').'_'.$instanceId,$this);
			}
		}else{
			// we are creating a completely new record
			if(is_array($params)){
				if(!static::$_columns){
					static::_loadColumns();
				}

				foreach($params as $key=>$val){
					$this->__set($key,$val);
				}
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
		if($force || $this->_id === null){
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

		// determine db table name (if not predefined)
		if(static::$_dbTable)
			$dbTable = static::$_dbTable;
		else
			$dbTable = static::_determineDbTable();

		if($this->_id){
			// save changes to DB
			$db = $this->_getDb(self::OP_UPDATE);
			$db->update(
				$dbTable,
				$changeData,
				$db->quoteInto(self::$_pk.' = ?',$this->_id)
			);

			// update cache (if enabled)
			if(static::$_cacheEnabled){
				static::_cacheSave($this->_data,$this->_id,self::OP_UPDATE);
			}
		}else{
			// create new DB row
			$db = $this->_getDb(self::OP_CREATE);
			$db->insert(
				$dbTable,
				$changeData
			);

			// retrieve object id
			if(!($this->_id = $db->lastInsertId($dbTable)))
				throw new Exception\Exception('Cannot retrieve new record id for table "'.$dbTable.'" (is the primary key set as AUTO_INCREMENT, IDENTITY or has a SEQUENCE?)');

			// retrieve rest of the object data from db (for auto-columns and such)
			$this->_loadFromDb();

			// store in registry
			if(static::$_persistent){
				Registry::set(strtr(get_called_class(),'\\','_').'_'.$this->_id,$this);
			}

			// save changes to cache (if enabled)
			if(static::$_cacheEnabled){
				static::_cacheSave($this->_data,$this->_id,self::OP_CREATE);
			}
		}


		$this->_changedData = array();
		$this->_loaded = true;

		return $this;
	}

	/**
	 * Returns record data as an array.
	 *
	 * @return array|null
	 */
	public function toArray(){
		if($this->_id && !$this->_loaded){
			$this->_loadFromDb();
		}
		return $this->_data;
	}


	/**
	 * @return int|null
	 */
	public function getId(){
		return $this->_id;
	}


	/**
	 * Load record data from database
	 *
	 * @param bool $force
	 * @throws Exception|NotFoundException
	 * @return void
	 */
	protected function _loadFromDb($force = false){
		if(!$this->_id)
			throw new Exception\Exception('Cannot load object from DB, because we dont know object ID.');
		elseif($this->_loaded && !$force)
			return;

		// determine db table name (if not predefined)
		if(static::$_dbTable)
			$dbTable = static::$_dbTable;
		else
			$dbTable = static::_determineDbTable();


		// load row from db
		$db = $this->_getDb(self::OP_READ);
		$select = $db->select()->from($dbTable)->where(static::$_pk.' = ?',$this->_id)->limit(1);
		$data = $select->query(\Zend\Db\Db::FETCH_ASSOC)->fetch();
		if(!is_array($data)){
			throw new Exception\NotFoundException($this->_id,get_class($this));
		}

		$this->_data = $data;
		$this->_loaded = true;

		// remember table columns
		if(!static::$_columns && !isset(self::$_columnsCache[get_called_class()])){
			self::$_columnsCache[get_called_class()] = array_keys($this->_data);
		}
	}


	/**
	 * Get the db adapter. Override this function to implement dynamic database adapter selection (for clustering,
	 * sharding, distributed, master-slave and other database configurations)
	 *
	 * Warning! This function gets called statically and dynamically.
	 *
	 * @throws Exception
	 * @param int $operation					Currently pending operation, one of the AbstractActiveRecord::OP_* constants
	 * @return string|\Zend\Db\Adapter\AbstractAdapter
	 */
	protected static function _getDb($operation = self::OP_OTHER){
		if(isset($this)){
			// dynamic call
			if(is_object($this->_db)){
				return $this->_db;
			}

			// use subclass default db (set via method)
			elseif(isset(self::$_subclassDefaultDb[get_called_class()])){
				$this->_db = self::$_subclassDefaultDb[get_called_class()];
			}

			// use subclass default db (set via property)
			elseif(static::$_defaultDb){
				if(is_object(static::$_defaultDb)){
					$this->_db = static::$_defaultDb;
				}elseif(is_scalar(static::$_defaultDb) && Registry::isRegistered(static::$_defaultDb)){
					// try to load db adapter from Zend\Registry
					$dbObj = Registry::get(static::$_defaultDb);
					if(is_object($dbObj) && $dbObj instanceof Zend\Db\Adapter\AbstractAdapter){
						static::$_defaultDb = $dbObj;
						$this->_db = $dbObj;
					}else{
						throw new Exception\Exception('Zend\Registry entry "'.static::$_defaultDb.'" is not a valid db adapter');
					}
				}else{
					throw new Exception\Exception('Cannot find db adapter in Zend\Registry using name "'.static::$_defaultDb.'"');

				}
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

			return $this->_db;
		}
		else{
			// static call

			// use subclass default db (set via method)
			if(isset(self::$_subclassDefaultDb[get_called_class()])){
				return self::$_subclassDefaultDb[get_called_class()];
			}

			// use subclass default db (set via property)
			elseif(static::$_defaultDb){
				if(is_object(static::$_defaultDb)){
					return static::$_defaultDb;
				}elseif(is_scalar(static::$_defaultDb) && Registry::isRegistered(static::$_defaultDb)){
					// try to load db adapter from Zend\Registry
					$dbObj = Registry::get(static::$_defaultDb);
					if(is_object($dbObj) && $dbObj instanceof Zend\Db\Adapter\AbstractAdapter){
						static::$_defaultDb = $dbObj;
						return $dbObj;
					}else{
						throw new Exception\Exception('Zend\Registry entry "'.$this->_db.'" is not a valid db adapter');
					}
				}else{
					throw new Exception\Exception('Cannot find db adapter in Zend\Registry using name "'.static::$_defaultDb.'"');
					
				}
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
	 * 
	 * Warning! This function is called statically and dynamically.
	 *
	 * @throws Exception
	 * @param int $operation
	 * @return bool|\Zend\Cache\Frontend\Core
	 */
	protected static function _getCache($operation = self::OP_OTHER){
		if(isset($this)){
			// cache is disabled
			if(!static::$_cacheEnabled || $this->_cache === false){
				return false;
			}

			// we already have a cache
			elseif(is_object($this->_cache)){
				return $this->_cache;
			}

			// use subclass default cache (set via method)
			elseif(isset(self::$_subclassDefaultCache[get_called_class()])){
				$this->_cache = self::$_subclassDefaultCache[get_called_class()];
			}

			// subclass default cache (set via property)
			elseif(static::$_defaultCache){
				if(is_object(static::$_defaultCache)){
					$this->_cache = static::$_defaultCache;
				}elseif(is_scalar(static::$_defaultCache) && Registry::isRegistered(static::$_defaultCache)){
					// try to load cache frontend from Zend\Registry
					$cacheObj = Registry::get(static::$_defaultCache);
					if(is_object($cacheObj) && $cacheObj instanceof Zend\Cache\Frontend\Core){
						static::$_defaultCache = $cacheObj;
						$this->_cache = $cacheObj;
					}else{
						throw new Exception\Exception('Zend\Registry entry named "'.static::$_defaultCache.'" is not a valid cache frontend');
					}
				}else{
					throw new Exception\Exception('Cannot find cache frontend in Zend\Registry using name "'.static::$_defaultCache.'"');
				}
			}


			// use global ActiveRecord default cache
			elseif(self::$_globalDefaultCache){
				$this->_cache = self::$_globalDefaultCache;
			}

			// do not use cache
			else{
				$this->_cache = false;
			}

			return $this->_cache;
		}else{
			// static call

			// use subclass default cache (set via method)
			if(isset(self::$_subclassDefaultCache[get_called_class()])){
				return self::$_subclassDefaultCache[get_called_class()];
			}

			// use subclass default cache (set via property)
			elseif(static::$_defaultCache){
				if(is_object(static::$_defaultCache)){
					return static::$_defaultCache;
				}elseif(is_scalar(static::$_defaultCache) && Registry::isRegistered(static::$_defaultCache)){
					// try to load cache frontend from Zend\Registry
					$cacheObj = Registry::get(static::$_defaultCache);
					if(is_object($cacheObj) && $cacheObj instanceof Zend\Cache\Frontend\Core){
						static::$_defaultCache = $cacheObj;
						return $cacheObj;
					}else{
						throw new Exception\Exception('Zend\Registry entry named "'.static::$_defaultCache.'" is not a valid cache frontend');
					}
				}else{
					throw new Exception\Exception('Cannot find cache frontend in Zend\Registry using name "'.static::$_defaultCache.'"');
				}
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
	 *
	 * ---=---=---=---=---=---=---=---=---=   STATIC METHODS BELOW ---=---=---=---=---=---=---=---=---=---=---=---
	 *
	 */

	/**
	* Return ActiveRecord instance. If supplied a numeric value or a string, an ActiveRecord with that id will
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
	 		$cacheId = strtr(get_called_class(),'\\','_').'_'.$param;
	 		
	 		if(
				static::$_persistent &&
				Registry::isRegistered($cacheId) &&
				($obj = Registry::get($cacheId))
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
		$cacheId = strtr(get_called_class(),'\\','_').'_'.$id;
		if(
			static::$_persistent &&
			Registry::isRegistered($cacheId) &&
			($obj = Registry::get($cacheId))
		){
			return $obj;
		}

		// try to load data from cache
		if(
			static::$_cacheEnabled &&
			($cache = static::_getCache(self::OP_READ)) &&
			($data = $cache->load($cacheId)) &&
			is_array(unserialize($data))
		){
			// create new object instance and inject its id and data
			$obj = new static($data,$id);

			// store it in registry
			if(static::$_persistent)
				Registry::set(get_called_class().'_'.$id,$obj);

			return $obj;
		}
		

		// determine db table name (if not predefined)
		if(static::$_dbTable)
			$dbTable = static::$_dbTable;
		else
			$dbTable = static::_determineDbTable();

		// load from db
		$db = static::_getDb(self::OP_READ);
		$result = $db->select()
					->from($dbTable)
					->where($db->quoteIdentifier(static::$_pk).' = ?',$id)
					->limit(1)
					->query(\Zend\Db\Db::FETCH_ASSOC)
					->fetch()
		;
		if(!is_array($result))
			return false;

		// create new object instance and inject its id and data
		$obj = new static($result,$id);

		// store data in cache
		if(static::$_cacheEnabled){
			static::_cacheSave($result,$id,self::OP_UPDATE);
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


	/**
	 * @static
	 * @param array $params			Query params in one of the following formats:
	 * 								1. SQL operators and parameter binding:
	 * 									$params = array(
	 * 										array('columnA = ?',  'value'),
	 * 										array('columnB != ?', 'value'), ...
	 * 									);
	 * 									...  WHERE columnA = "value" AND columnB != "value"
	 *
	 * 								2. Exact column value match:
	 * 									$params = array(
	 * 										array('columnA', 'value'),
	 * 										array('columnB', array('foo','bar')),
	 * 									);
	 * 									... WHERE columnA = "value" AND columnB IN ("foo","bar")
	 *
	 * 								3. Column comparison expressions:
	 * 									$params = array(
	 * 										array('columnA', 'eq',   'value'),
	 * 										array('columnB', 'like', '%value%'),
	 * 										array('columnC', 'gt',   15),
	 * 									);
	 * 									... WHERE columnA = "value" AND columnB LIKE "%value%" AND columnC > 15
	 *
	 * 									Available comparison expressions:
	 * 										eq  ne  gt  lt  gte  lte  like  notLike  null  isNull  notNull isNotNull
	 * 										=   !=  >   <   >=   <=   ~     !~
	 * 												
	 * @param array $options
	 * @return \Zend\Db\ActiveRecord\Collection
	 */
	public static function findAll($params = array(),$options = array()){
		// get db adapter
		$db = static::_getDb(self::OP_READ);

		// get db table name
		if(static::$_dbTable){
			$dbTable = static::$_dbTable;
		}else{
			$dbTable = static::_determineDbTable();
		}

		// prepare select statement (select table, columns etc.)
		$select = static::_prepareFindSelect($params, $options, $dbTable, $db);

		// process supplied parameters (search parameters)
		static::_prepareFindParameters($select, $params, $options, $db);

		// apply supplied options (like LIMIT, ORDER and such)
		static::_prepareFindOptions($select, $params, $options, $db);

		// query the db
		$data = $select->query(\Zend\Db\Db::FETCH_ASSOC)->fetchAll();

		// return collection
		$col = new static::$_collectionClass($data,get_called_class());
		return $col;
	}

	/**
	 * Prepare and return a Zend\Db\Select statement that will be used to find records.
	 *
	 * @static
	 * @param array $params
	 * @param array $options
	 * @param string $dbTable
	 * @param \Zend\Db\Adapter\AbstractAdapter $db
	 * @param string $mainTableAlias
	 * @return \Zend\Db\Select
	 */
	protected static function _prepareFindSelect($params = array(), $options = array(), $dbTable, \Zend\Db\Adapter\AbstractAdapter &$db, $mainTableAlias = 'mtbl.'){
		$mainTableAlias = rtrim($mainTableAlias,'.');
		$select = $db->select();

		if(!empty($options['calcTotal'])){
			// SELECT SQL_CALC_FOUND_ROWS ... FROM ...
			if(isset($options['count'])){
				$select->from(
					array($mainTableAlias=>$dbTable),
					array(new Zend\Db\Expr('SQL_CALC_FOUND_ROWS COUNT('.$mainTableAlias.'.'.$db->quoteIdentifier(static::$_pk).')'))
				);
			}elseif(array_key_exists('light',$options) && $options['light']){
				$select->from(
					array($mainTableAlias=>$dbTable),
					array(new Zend\Db\Expr('SQL_CALC_FOUND_ROWS '.$mainTableAlias.'.'.$db->quoteIdentifier(static::$_pk)))
				);
			}else{
				$select->from(
					array($mainTableAlias=>$dbTable),
					array(new Zend\Db\Expr('SQL_CALC_FOUND_ROWS '.$mainTableAlias.'.*'))
				);
			}
		}else{
			// SELECT ... FROM ...
			if(isset($options['count'])){
				$select->from(
					array($mainTableAlias=>$dbTable),
					array('COUNT('.$mainTableAlias.'.'.static::$_pk.')')
				);
			}elseif(array_key_exists('light',$options) && $options['light']){
				$select->from(
					array($mainTableAlias=>$dbTable),
					array($mainTableAlias.'.'.static::$_pk)
				);
			}else{
				$select->from(
					array($mainTableAlias=>$dbTable),
					array($mainTableAlias.'.*')
				);
			}
		}
		if(isset($options['leftJoin'])){
			if(
			   !is_array($options['leftJoin']) ||
			   empty($options['leftJoin'][0]) ||
			   !is_array($options['leftJoin'][0]) ||
			   empty($options['leftJoin'][0][0]) ||
			   !is_array($options['leftJoin'][0][0])
			){
				$options['leftJoin'] = array($options['leftJoin']);
			}

			foreach($options['leftJoin'] as $join){
				call_user_func_array(array($select,'joinLeft'),$join);
			}

		}

		if(isset($options['innerJoin'])){
			if(
			   !is_array($options['innerJoin']) ||
			   !is_array($options['innerJoin'][0]) ||
			   !is_array($options['innerJoin'][0][0])
			){
				$options['innerJoin'] = array($options['innerJoin']);
			}

			foreach($options['innerJoin'] as $join){
				call_user_func_array(array($select,'joinInner'),$join);
			}
		}

		return $select;
	}

	/**
	 * Prepares and modifies the supplied Select statement, according to search parameters.
	 * Available search methods are described in findAll() method.
	 *
	 * @static
	 * @throws Exception\BadMethodCallException
	 * @param \Zend\Db\Select $select
	 * @param array $params
	 * @param array $options
	 * @param \Zend\Db\Adapter\AbstractAdapter $db
	 * @param string $mainTableAlias
	 * @return void
	 */
	protected static function _prepareFindParameters(\Zend\Db\Select &$select, $params = array(), $options = array(), \Zend\Db\Adapter\AbstractAdapter &$db, $mainTableAlias = 'mtbl.'){
		$mainTableAlias = rtrim($mainTableAlias,'.').'.';

		// determine main table columns
		if(static::$_columns){
			$columns = static::$_columns;
		}else{
			$columns = static::_loadColumns();
		}
		$prefixes = array(
			$mainTableAlias => $columns
		);

		// add extra tables
		if(isset($options['leftJoin'])){
			if(
				is_array($options['leftJoin']) &&
				!empty($options['leftJoin'][0]) &&
				is_array($options['leftJoin'][0]) &&
				!empty($options['leftJoin'][0][0]) &&
				is_array($options['leftJoin'][0][0])
			){
				foreach($options['leftJoin'] as $joinSpec){
					$prefix = rtrim(current(array_keys($joinSpec[0])),'.').'.';
					$prefixes[$prefix] = static::_getTableColumns(current(array_values($joinSpec[0])),$db);
				}
			}
		}
		if(isset($options['innerJoin'])){
			if(
			   is_array($options['innerJoin']) &&
			   !empty($options['innerJoin'][0]) &&
			   is_array($options['innerJoin'][0]) &&
			   !empty($options['innerJoin'][0][0]) &&
			   is_array($options['innerJoin'][0][0])
			){
				foreach($options['innerJoin'] as $joinSpec){
					$prefix = rtrim(current(array_keys($joinSpec[0])),'.').'.';
					$prefixes[$prefix] = static::_getTableColumns(current(array_values($joinSpec[0])),$db);
				}
			}
		}


		foreach($prefixes as $prefix => $columns){
			$nextParams = array();
			foreach($params as $param => $val){
				// determine query type
				if(!is_numeric($param) && strstr($param,' ') && $val){
					// SQL parameter (no further parsing)
					$select->where($param,$val);
				}

				// exact match search
				elseif(!is_numeric($param)){
					// check if this is a joined-table parameter
					$oParam = $param;
					if(strstr($param,'.')){
						if(substr($param,0,strpos($param,'.')+1) != $prefix){
							// this is not the prefix we are looking for
							continue;
						}else{
							$param = substr($param,strpos($param,'.')+1);
						}
					}

					// check if such column exists
					if(in_array($param,$columns)){
						$select->where($prefix.$db->quoteIdentifier($param).(is_array($val) ? ' IN ( ? ) '  : ' = ?' ),$val);
					}else{
						// try this field with next table
						$nextParams[$oParam] = $val;
					}

				// search expression
				}elseif(count($val) === 3){
					if(in_array($val[0],$columns)){
						$col = $val[0];
						$expr = $val[1];
						$val = $val[2];
						
						switch(strtolower($expr)){
							case 'eq':
							case '=':
							case 'equals':
							case 'in':
								$select->where($prefix.$db->quoteIdentifier($col).(is_array($val) ? ' IN ( ? ) '  : ' = ?' ),$val);
								break;

							case 'ne':
							case '!=':
							case 'notequals':
							case 'notin':
							case 'not':
								$select->where($prefix.$db->quoteIdentifier($col).(is_array($val) ? ' NOT IN ( ? ) '  : ' != ?' ),$val);
								break;
								
							case 'like':
							case '~':
							case '~=':
							case 'islike':
								$select->where($prefix.$db->quoteIdentifier($col).' LIKE ?',$val);
								break;
							
							case 'notlike':
							case '!~':
							case '!~=':
							case 'isnotlike':
								$select->where($prefix.$db->quoteIdentifier($col).' NOT LIKE ?',$val);
								break;
								
							case 'gt':
							case 'greater':
							case '>':
							case 'greaterthan':
								$select->where($prefix.$db->quoteIdentifier($col).' > ?',$val);
								break;
								
							case 'lt':
							case 'less':
							case '<':
							case 'lessthan':
								$select->where($prefix.$db->quoteIdentifier($col).' < ?',$val);
								break;
							
							case 'gte':
							case 'greaterorequals':
							case 'greaterorequal':
							case '>=':
							case '=>':
								$select->where($prefix.$db->quoteIdentifier($col).' >= ?',$val);
								break;
							
							case 'lte':
							case 'lessorequals':
							case 'lessorequal':
							case '<=':
							case '=<':
								$select->where($prefix.$db->quoteIdentifier($col).' <= ?',$val);
								break;
							
							case 'empty':
							case 'isempty':
								$select->where($prefix.$db->quoteIdentifier($col).' IS NULL OR '.$prefix.$db->quoteIdentifier($col).' = ""');
								break;
								
							case 'notempty':
							case 'isnotempty':
								$select->where($prefix.$db->quoteIdentifier($col).' IS NULL OR '.$prefix.$db->quoteIdentifier($col).' = ""');
								break;

							case 'null':
							case 'isnull':
								$select->where($prefix.$db->quoteIdentifier($col).' IS NULL');
								break;

							case 'notnull':
							case 'isnotnull':
								$select->where($prefix.$db->quoteIdentifier($col).' IS NOT NULL');
								break;

							case 'bitand':
							case 'bitadd':
								$select->where($prefix.$db->quoteIdentifier($col).' & ?',$val);
								break;

							default:
								throw new Exception\BadMethodCallException('Unknown search expression "'.$expr.'"');
								break;
						}

					}else{
						// could not match this parameter to any columns. Try with next table
						$nextParams[] = $val;
					}
				}else{
					throw new Exception\BadMethodCallException('Did not understand the supplied search parameters');
				}
				
				$params = $nextParams;
			}
		}
	}

	/**
	 * Prepares and modifies the supplied Select statement, according to search options (LIMIT, ORDER etc.)
	 * Available search options and modes are described in findAll() method.
	 *
	 * @static
	 * @param \Zend\Db\Select $select
	 * @param array $params
	 * @param array $options
	 * @param \Zend\Db\Adapter\AbstractAdapter $db
	 * @param string $mainTableAlias
	 * @return void
	 */
	protected static function _prepareFindOptions(\Zend\Db\Select &$select, $params = array(), $options = array(), \Zend\Db\Adapter\AbstractAdapter &$db, $mainTableAlias = 'mtbl.'){
		$mainTableAlias = rtrim($mainTableAlias,'.').'.';

		// determine main table columns
		if(static::$_columns){
			$columns = static::$_columns;
		}else{
			$columns = static::_loadColumns();
		}
		$prefixes = array(
			$mainTableAlias => $columns
		);

		// add extra tables
		if(isset($options['leftJoin'])){
			if(
				is_array($options['leftJoin']) &&
				!empty($options['leftJoin'][0]) &&
				is_array($options['leftJoin'][0]) &&
				!empty($options['leftJoin'][0][0]) &&
				is_array($options['leftJoin'][0][0])
			){
				foreach($options['leftJoin'] as $joinSpec){
					$prefix = rtrim(current(array_keys($joinSpec[0])),'.').'.';
					$prefixes[$prefix] = static::_getTableColumns(current(array_values($joinSpec[0])),$db);
				}
			}
		}
		if(isset($options['innerJoin'])){
			if(
			   is_array($options['innerJoin']) &&
			   !empty($options['innerJoin'][0]) &&
			   is_array($options['innerJoin'][0]) &&
			   !empty($options['innerJoin'][0][0]) &&
			   is_array($options['innerJoin'][0][0])
			){
				foreach($options['innerJoin'] as $joinSpec){
					$prefix = rtrim(current(array_keys($joinSpec[0])),'.').'.';
					$prefixes[$prefix] = static::_getTableColumns(current(array_values($joinSpec[0])),$db);
				}
			}
		}


		if(array_key_exists('order',$options)){
			// normalize order params
			if(array_key_exists('orderBy',$options)){
				$options['order'] = $options['orderBy'];
			}

			if(
				!array_key_exists('orderDir',$options) ||
				!in_array(strtolower($options['orderDir']),array('asc','desc'))
			){
				$options['orderDir'] = 'asc';
			}


			// check if order is an array
			if(!is_array($options['order'])){
				$options['order'] = array(
					array($options['order'], $options['orderDir'])
				);

			// check if supplied only one array specification - i.e. array('column','desc')
			}elseif(
				is_array($options['order']) &&
				isset($options['order'][0]) &&
				!is_array($options['order'][0])
			){
				$options['order'] = array(
					$options['order']
				);
			}
			
			// map order params
			$order = array();
			foreach($options['order'] as $foo){
				if(!is_array($foo)){
					$orderCol = $foo;
					$orderDir = $options['orderDir'];
				}elseif(empty($foo[1])){
					$orderCol = $foo[0];
					$orderDir = $options['orderDir'];
				}else{
					$orderCol = $foo[0];
					$orderDir = $foo[1];
				}

				foreach($prefixes as $prefix => $cols){
					// SQL expression
					if(is_object($orderCol) && ($orderCol instanceof \Zend\Db\Expr)){
						$order[] = $orderCol;

					// column name with a prefix
					}elseif(stristr($orderCol,'.')){
						// -- explicit prefix supplied
						if(substr($orderCol,0,strpos($orderCol,'.')+1) != $prefix){
							// prefix mismatch
							continue;
						}

						$order[] =
							$prefix.substr($orderCol,strpos($orderCol,'.')+1).' '.
							$orderDir
						;
						continue 2;


					// column name
					}elseif(in_array($orderCol,$cols)){
						$order[] =
							$prefix
							. $orderCol
							. ' '
							. $orderDir
						;
						continue 2;

					// rand() alias
					}elseif(strtolower($orderCol) == 'rand()'){
						$order[] = 'RAND() '.$orderDir;

					// unrecognised order column
					}
					/*else{
						throw new Exception\BadMethodCallException('Did not understand')
					}*/
				}
			}
			
			if(count($order)){
				$select->order($order);
			}else{
				throw new Exception\BadMethodCallException('Did not understand "order" option - is the column name correct?');
			}
		}

		// limit
		if(array_key_exists('limit',$options) && array_key_exists('offset',$options)){
			$select->limit($options['limit'],$options['offset']);

		// limit + offset
		}elseif(array_key_exists('limit',$options)){
			$select->limit($options['limit']);
		}
	}



	public static function findOne($params = array(), $options = array()){
		$options['limit'] = 1;
		$result = static::findAll($params,$options);
		return $result->first();
	}


	/**
	 * Save column information to cache
	 *
	 * @return bool
	 */
	protected static function _cacheSaveColumns(){
		if($cache = static::_getCache(self::OP_META)){
			return $cache->save(serialize(self::$_columnsCache[get_called_class()]),strtr(get_called_class(),'\\','_').'_columns');
		}
		return false;
	}

	/**
	 * Return db table name used by this class.
	 *
	 * @static
	 * @return string
	 */
	public static function getDbTable(){
		if(static::$_dbTable)
			return static::$_dbTable;
		else
			return static::_determineDbTable();
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
			self::$_subclassDefaultDb[get_called_class()] = $db;
	}

	/**
	 * Set the default db adapter to use for all active record instances
	 * @static
	 * @param null|\Zend\Db\Adapter\AbstractAdapter
	 * @return void
	 */
	public static function setDefaultAdapter(Zend\Db\Adapter\AbstractAdapter $db = null){
		self::setDefaultDb($db);
	}

	/**
	 * @static
	 * @return \Zend\Db\Adapter\AbstractAdapter|null
	 */
	public static function getDefaultDb(){
		// subclass default (set via method)
		if(get_called_class() !== __CLASS__ && isset(self::$_subclassDefaultDb[get_called_class()])){

			return self::$_subclassDefaultDb[get_called_class()];

		// subclass default (set via property)
		}elseif(get_called_class() !== __CLASS__ && static::$_defaultDb){

			if(is_object(static::$_defaultDb)){
				return static::$_defaultDb;
			}elseif(is_scalar(static::$_defaultDb) && Registry::isRegistered(static::$_defaultDb)){
				// try to load db adapter from Zend\Registry
				$dbObj = Registry::get(static::$_defaultDb);
				if(is_object($dbObj) && $dbObj instanceof Zend\Db\Adapter\AbstractAdapter){
					static::$_defaultDb = $dbObj;
					return $dbObj;
				}else{
					throw new Exception\Exception('Cannot find db adapter in Zend\Registry using name "'.static::$_defaultDb.'"');
				}
			}else{
				throw new Exception\Exception('Zend\Registry entry "'.static::$_defaultDb.'" is not a valid db adapter');
			}

		// global ActiveRecord default
		}else{
			return self::$_globalDefaultDb;
		}
	}

	/**
	 * @static
	 * @return \Zend\Db\Adapter\AbstractAdapter|null
	 */
	public static function getDefaultAdapter(){
		return self::getDefaultDb();
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
			self::$_subclassDefaultCache[get_called_class()] = $cache;
	}

	/**
	 * @static
	 * @return Zend\Cache\Frontend\Core
	 */
	public static function getDefaultCache(){
		// subclass default (set via class)
		if(get_called_class() !== __CLASS__ && isset(self::$_subclassDefaultCache[get_called_class()])){
			return self::$_subclassDefaultCache[get_called_class()];

		// subclass default (set via property)
		}elseif(get_called_class() !== __CLASS__ && static::$_defaultCache){
			if(is_object(static::$_defaultCache)){
				return static::$_defaultCache;
			}elseif(is_scalar(static::$_defaultCache) && Registry::isRegistered(static::$_defaultCache)){
				// try to load cache frontend from Zend\Registry
				$cacheObj = Registry::get(static::$_defaultCache);
				if(is_object($cacheObj) && $cacheObj instanceof Zend\Cache\Frontend\Core){
					static::$_defaultCache = $cacheObj;
					return $cacheObj;
				}else{
					throw new Exception\Exception('Zend\Registry entry named "'.static::$_defaultCache.'" is not a valid cache frontend');
				}
			}else{
				throw new Exception\Exception('Cannot find cache frontend in Zend\Registry using name "'.static::$_defaultCache.'"');
			}

		// global ActiveRecord cache
		}else{
			return self::$_globalDefaultCache;
		}
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

	/**
	 * Store record data in cache
	 *
	 * @param $data
	 * @param $id
	 * @return void
	 */
	protected static function _cacheSave($data,$id, $op = self::OP_CREATE){
		if($cache = static::_getCache($op)){
			$cache->save(serialize($data),strtr(get_called_class(),'\\','_').'_'.$id);
		}
	}


	/**
	 * Load column names from cache
	 *
	 * @return array|bool
	 */
	protected static function _cacheLoadColumns(){
		if($cache = static::_getCache(self::OP_META)){
			if(($columns = $cache->load(strtr(get_called_class(),'\\','_').'_columns'))){
				self::$_columnsCache[get_called_class()] = unserialize($columns);
				return self::$_columnsCache[get_called_class()];
			}
		}
		return false;
	}

	/**
	 * Load columns (properties) info from cache or database.
	 *
	 * @static
	 * @throws Exception
	 * @return array
	 */
	protected static function _loadColumns(){
		$class = get_called_class();
		// load columns from cache
		if(static::_cacheLoadColumns()){
			return self::$_columnsCache[$class];
		}

		// determine db table name (if not predefined)
		if(static::$_dbTable)
			$dbTable = static::$_dbTable;
		else
			$dbTable = static::_determineDbTable();

		// load columns from db
		$db = static::_getDb(self::OP_META);
		$meta = $db->describeTable($dbTable);
		if(!count($meta))
			throw new Exception\Exception('Cannot determine table "'.$dbTable.'" columns for class "'.$class.'"');

		self::$_columnsCache[$class] = array();
		foreach($meta as $column => $props){
			self::$_columnsCache[$class][$column] = true;
		}

		static::_cacheSaveColumns();

		return self::$_columnsCache[$class];
	}

	/**
	 * Read from db and return table columns.
	 *
	 * @static
	 * @throws Exception\Exception
	 * @param $tableName
	 * @param \Zend\Db\Adapter\AbstractAdapter $db
	 * @return array
	 */
	protected static function _getTableColumns($tableName,\Zend\Db\Adapter\AbstractAdapter $db){
		$meta = $db->describeTable($dbTable);
		if(!count($meta))
			throw new Exception\Exception('Cannot determine table "'.$dbTable.'" columns');

		$result = array();
		foreach($meta as $column => $props){
			$result[] = $column;
		}

		return $result;
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
			if(static::$_columns){
				// check if column (property) exists before setting it
				if(isset(static::$_columns[$prop])){
					return $this->_data[$prop] = $val;
				}else{
					throw new Exception\UndefinedPropertyException(get_class($this),$prop);
				}
			}else{
				// we should determine columns
				static::_loadColumns();
				if(isset(self::$_columnsCache[get_class($this)][$prop])){
					return $this->_data[$prop] = $val;
				}else{
					throw new Exception\UndefinedPropertyException(get_class($this),$prop);
				}
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


