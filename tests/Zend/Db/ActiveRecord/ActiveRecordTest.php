<?php

/**
 * @namespace
 */
namespace ZendTest\Db\ActiveRecord;

/**
 * @use
 */
use \Zend\Db;
use \Zend\Db\ActiveRecord;
use \Zend\Db\ActiveRecord\AbstractActiveRecord;
use \Zend\Cache;

class ActiveRecordTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * Tables created during test
	 * @var array
	 */
	protected $_tables = array();

	/**
	 * @var \Zend\Db\Adapter\AbstractAdapter
	 */
	protected $_db;

	public function setUp(){
		if(!defined('TESTS_ZEND_DB_ACTIVERECORD_ENABLED')){
			return $this->markTestSkipped('Invalid configuration!');
		}elseif(!TESTS_ZEND_DB_ACTIVERECORD_ENABLED){
			return $this->markTestSkipped('ActiveRecord tests are currently disabled. You can enable them'.
											' by setting up database connection in TestConfiguration.php');
		}
		// create default db adapter
		$this->_db = $this->_createDbAdapter();

		// set it as default adapter for all ActiveRecord instances
		AbstractActiveRecord::setDefaultDb($this->_db);

		// clear registry
		\Zend\Registry::_unsetInstance();
	}
	
	public function tearDown(){
		if ($this->_db) {
			foreach($this->_tables as $t){
				$this->_db->query('DROP TEMPORARY TABLE '.$t);
			}

			$this->_db->closeConnection();
			$this->_db = null;			
		}

		// remove default db adapters
		AbstractActiveRecord::setDefaultCache();
		AbstractActiveRecord::setDefaultDb();

		// clear registry
		\Zend\Registry::_unsetInstance();
	}

	/**
	 *  A global db adapter can be set for all ActiveRecord instances.
	 * @return void
	 */
	public function testDefaultGlobalAdapter(){
		AbstractActiveRecord::setDefaultDb($this->_db);
		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());

		AbstractActiveRecord::setDefaultDb();
		$this->assertNull(AbstractActiveRecord::getDefaultDb());
	}

	/**
	 * AbstractActiveRecord subclasses can have their own default db adapter set via static method
	 * @return void
	 */
	public function testSubclassDefaultAdapter(){
		AbstractActiveRecord::setDefaultDb($this->_db);
		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());

		$otherDb = $this->_createDbAdapter();
		$this->assertNotSame($this->_db,$otherDb);

		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());

		TestAsset\Basic::setDefaultDb($otherDb);
		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());
		$this->assertSame($otherDb,TestAsset\Basic::getDefaultDb());
		$this->assertNotSame($this->_db,TestAsset\Basic::getDefaultDb());

		// reset default db for Basic class
		TestAsset\Basic::setDefaultDb();

		// Basic class should now use global default db adapter
		$this->assertSame($this->_db,TestAsset\Basic::getDefaultDb());

		// destroy the other db
		unset($otherDb);
	}

	/**
	 * AbstractActiveRecord subclasses can have default db adapter set via $_defaultDb property
	 * @return void
	 */
	public function testSubclassDefaultAdapterSetByProperty(){
		AbstractActiveRecord::setDefaultDb($this->_db);
		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());

		$otherDb = $this->_createDbAdapter();
		$this->assertNotSame($this->_db,$otherDb);

		// save the adapter in registry
		\Zend\Registry::set('otherdb',$otherDb);

		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());
		$this->assertSame($this->_db,TestAsset\Basic::getDefaultDb());
		$this->assertSame($otherDb,TestAsset\DefaultDb::getDefaultDb());

		// set another default db
		TestAsset\DefaultDb::setDefaultDb($this->_db);
		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());
		$this->assertSame($this->_db,TestAsset\Basic::getDefaultDb());
		$this->assertSame($this->_db,TestAsset\DefaultDb::getDefaultDb());

		// change it back to class default
		TestAsset\DefaultDb::setDefaultDb();
		$this->assertSame($this->_db,AbstractActiveRecord::getDefaultDb());
		$this->assertSame($this->_db,TestAsset\Basic::getDefaultDb());
		$this->assertSame($otherDb,TestAsset\DefaultDb::getDefaultDb());


		// destroy the other db connection
		unset($otherDb);
	}

	/**
	 *  A global Cache adapter can be set for all ActiveRecord instances.
	 * @return void
	 */
	public function testDefaultGlobalCache(){
		$cache = Cache\Cache::factory('Core','BlackHole');
		AbstractActiveRecord::setDefaultCache($cache);
		$this->assertSame($cache,AbstractActiveRecord::getDefaultCache());
	}

	/**
	 * AbstractActiveRecord Subclasses can have their own default Cache adapter set
	 * @return void
	 */
	public function testSubclassDefaultCache(){
		$cache1 = Cache\Cache::factory('Core','BlackHole');
		$cache2 = Cache\Cache::factory('Core','BlackHole');
		$this->assertNotSame($cache1,$cache2);

		AbstractActiveRecord::setDefaultCache($cache1);
		$this->assertSame($cache1,AbstractActiveRecord::getDefaultCache());

		TestAsset\Basic::setDefaultCache($cache2);
		$this->assertSame($cache1,AbstractActiveRecord::getDefaultCache());
		$this->assertSame($cache2,TestAsset\Basic::getDefaultCache());
		$this->assertNotSame($cache1,TestAsset\Basic::getDefaultCache());

		TestAsset\Basic::setDefaultCache();
	}

	/**
	 * AbstractActiveRecord Subclasses can have their own default Cache adapter set
	 * @return void
	 */
	public function testSubclassDefaultCacheViaProperty(){
		$cache1 = Cache\Cache::factory('Core','BlackHole');
		$cache2 = Cache\Cache::factory('Core','BlackHole');
		$this->assertNotSame($cache1,$cache2);

		// store the second frontend to registry
		\Zend\Registry::set('othercache',$cache2);

		// set global default cache
		AbstractActiveRecord::setDefaultCache($cache1);

		// check default caches of different subclasses
		$this->assertSame($cache1,AbstractActiveRecord::getDefaultCache());
		$this->assertSame($cache1,TestAsset\Basic::getDefaultCache());
		$this->assertSame($cache2,TestAsset\DefaultCache::getDefaultCache());

		// change the default cache
		TestAsset\DefaultCache::setDefaultCache($cache1);
		$this->assertSame($cache1,AbstractActiveRecord::getDefaultCache());
		$this->assertSame($cache1,TestAsset\Basic::getDefaultCache());
		$this->assertSame($cache1,TestAsset\DefaultCache::getDefaultCache());

		// change it back to class default
		TestAsset\DefaultCache::setDefaultCache();
		$this->assertSame($cache2,TestAsset\DefaultCache::getDefaultCache());

	}

	/**
	 * Using pre-defined db table name in subclass
	 *
	 * @depends testDefaultGlobalAdapter
	 * @return void
	 */
	public function testStaticTableName(){
		$this->_createTableForStatic();
		$obj = new TestAsset\StaticTableName();
		$this->assertSame('app_stats',TestAsset\StaticTableName::getDbTable());
		$obj->save();	// INSERT INTO app_stats ...
	}

	/**
	 * Using dynamic db table name, determined via custom method
	 *
	 * @depends testDefaultGlobalAdapter
	 * @return void
	 */
	public function testDynamicTableName(){
		$this->_createTableForDynamic();
		$obj = new TestAsset\DynamicTableName();
		$this->assertSame('app_stats_'.date('Y'),TestAsset\DynamicTableName::getDbTable());
		$obj->save();	// INSERT INTO app_stats ...
	}

	/**
	 * Create an empty db record with the simplest subclass possible.
	 *
	 * @return testDynamicTableName
	 * @return void
	 */
	public function testBasicRecordInsert(){
		$this->_createTableForBasic();
		$obj = new TestAsset\Basic();
		$this->assertInstanceOf('\Zend\Db\ActiveRecord\AbstractActiveRecord',$obj);
		$obj->save();

		$this->assertEquals(1,$obj->id);
		$this->assertEquals(1,$this->_db->fetchOne('SELECT COUNT(*) FROM '.$this->_db->quoteIdentifier('basic')));
	}

	/**
	 * @depends testBasicRecordInsert
	 * @expectedException Zend\Db\ActiveRecord\Exception\UndefinedPropertyException
	 * @return void
	 */
	public function testUnknownPropertyGet(){
		$this->_createTableForBasic();
		$obj = new TestAsset\Basic();
		$obj->name = 'Foo';
		$this->assertSame('Foo',$obj->name);
		$this->assertNull($obj->bar);	// throws UndefinedPropertyException
	}

	/**
	 * @depends testBasicRecordInsert
	 * @expectedException Zend\Db\ActiveRecord\Exception\UndefinedPropertyException
	 * @return void
	 */
	public function testUnknownPropertySet(){
		$this->_createTableForBasic();
		$obj = new TestAsset\Basic();
		$this->assertSame(TestAsset\Basic::getDefaultDb(),AbstractActiveRecord::getDefaultDb());
		$this->assertSame(TestAsset\Basic::getDefaultDb(),$this->_db);
		$obj->bar = 15;	// throws UndefinedPropertyException
	}

	/**
	 * @depends testBasicRecordInsert
	 * @return void
	 */
	public function testPersistence(){
		$this->_createTableForBasic();
		$this->_createTableForNonPersistent();

		// create a persistent object
		$obj = new TestAsset\Basic();
		$obj->save();
		$this->assertNotEmpty($id = $obj->id);

		// try to create another instance
		$obj2 = TestAsset\Basic::findById($id);

		// both objects should have the same id
		$this->assertSame($obj->id,$obj2->id);

		// both variables should point to the same object
		$this->assertSame($obj,$obj2);

		// -- Tests on non-persistent class

		// create a non-persistent object
		$obj = new TestAsset\NonPersistent();
		$obj->save();
		$this->assertNotEmpty($id = $obj->id);

		// try to create another instance
		$obj2 = TestAsset\NonPersistent::findById($id);

		// both objects should have the same id
		$this->assertSame($obj->id,$obj2->id);

		// both variables should NOT point to the same object
		$this->assertNotSame($obj,$obj2);
	}

	/**
	 * Create a new non-persistent record, save it to db (insert) and the load it
	 * back in another object instance.
	 *
	 * @depends testBasicRecordInsert
	 * @depends testPersistence
	 * @return void
	 */
	public function testRecordInsertAndLoad(){
		$this->_createTableForNonPersistent();
		$obj = new TestAsset\NonPersistent();
		$uniqueId = mt_rand(1,10000000);
		$obj->uniqueid = $uniqueId;
		$obj->save();
		$id = $obj->id;

		$obj2 = TestAsset\NonPersistent::findById($id);
		$this->assertInstanceOf('\\Zend\\Db\\ActiveRecord\\AbstractActiveRecord',$obj2);
		$this->assertInstanceOf('\\ZendTest\\Db\\ActiveRecord\\TestAsset\\NonPersistent',$obj2);
		$this->assertSame($id,$obj2->id);
		$this->assertSame($uniqueId,$obj2->uniqueid);
	}

	//public function testCache

	/**
	 * @depends testRecordInsertAndLoad
	 * @return void
	 */
	public function testLazyLoading(){
		// turn on profiling
		$profiler = $this->_db->getProfiler();
		$profiler->setEnabled(true);
		$this->assertEquals(0,$profiler->getTotalNumQueries());

		// create table
		$this->_createTableForBasic();

		// insert row into table
		$name = uniqid();
		$this->_db->insert('basic',array('name'=>$name));
		$id = $this->_db->lastInsertId('basic');
		$this->assertEquals(2,$profiler->getTotalNumQueries());

		// instantiate object but do not load it
		$obj = TestAsset\Basic::factory($id);
		$this->assertEquals(2,$profiler->getTotalNumQueries());

		// fetch a property, forcing the object to load
		$this->assertSame($name,$obj->name);
		$this->assertEquals(3,$profiler->getTotalNumQueries());
	}


	/**
	 * @depends testPersistence
	 * @depends testRecordInsertAndLoad
	 * @return void
	 */
	public function testFindById(){
		$this->_createTableForBasic();

		// turn on profiling
		$profiler = $this->_db->getProfiler();
		$profiler->setEnabled(true);
		$this->assertEquals(0,$profiler->getTotalNumQueries());

		// populate db table
		$count = 10;
		$ids = array();
		for($x=1;$x<=$count;$x++){
			$name = uniqid();
			$this->_db->insert('basic',array('name'=>$name));
			$id = $this->_db->lastInsertId('basic');
			$ids[$id] = $name;
		}

		$this->assertEquals($count,$profiler->getTotalNumQueries());

		// load each object from db
		$keys = array_rand($ids,$count);
		$x = 1;
		foreach($keys as $key){
			$obj = TestAsset\Basic::findById($key);
			$this->assertSame($ids[$key],$obj->name);
			$this->assertEquals($count + $x,$profiler->getTotalNumQueries());
			$x++;
		}

		// this class is a persistent ActiveRecord, so instantiating object again
		// should not reload it from db.
		$keys = array_rand($ids,$count);
		foreach($keys as $key){
			$obj = TestAsset\Basic::findById($key);
			$this->assertSame($ids[$key],$obj->name);
			$this->assertEquals($count + $x - 1,$profiler->getTotalNumQueries());
		}
	}

	/**
	 * @depends testRecordInsertAndLoad
	 * @depends testPersistence
	 * @return void
	 */
	public function testFindAll(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// send query and return a collection of records
		$all = TestAsset\Basic::findAll();
		$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$all);
		$this->assertEquals(count($data),$all->count());

		// inspect data
		$x=0;
		foreach($data as $recordData){
			$record = $all[$x++];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			foreach($recordData as $k=>$v){
				$this->assertEquals($v,$record->{$k});
			}
		}
	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindByExactMatch(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// search on object name
		foreach($data as $recordData){
			$search = TestAsset\Basic::findAll(array(
				'name' => $recordData['name']
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertEquals(1,$search->count());

			$record = $search[0];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			foreach($recordData as $k=>$v){
				$this->assertEquals($v,$record->{$k});
			}
		}

		// search on non-unique property
		$occupations = array();
		foreach($data as $recordData){
			if(isset($occupations[$recordData['occupation']])){
				$occupations[$recordData['occupation']]++;
			}else{
				$occupations[$recordData['occupation']] = 1;
			}
		}

		foreach($occupations as $occupation=>$count){
			$search = TestAsset\Basic::findAll(array(
				'occupation' => $occupation
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertEquals($count,$search->count());
		}

	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindByExactMatchOnTwoProperties(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// search on object name
		foreach($data as $recordData){
			$search = TestAsset\Basic::findAll(array(
				'name' => $recordData['name'],
				'occupation' => $recordData['occupation'],
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertEquals(1,$search->count());

			$record = $search[0];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			foreach($recordData as $k=>$v){
				$this->assertEquals($v,$record->{$k});
			}
		}
	}


	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindWithSQLExpression(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// search using SQL expression
		foreach($data as $recordData){
			$search = TestAsset\Basic::findAll(array(
				'name = ?' => $recordData['name'],
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertEquals(1,$search->count());

			$record = $search[0];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			foreach($recordData as $k=>$v){
				$this->assertEquals($v,$record->{$k});
			}
		}
	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindWithExpressionEq(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// search using expressions
		foreach($data as $recordData){
			$search = TestAsset\Basic::findAll(array(
				array('name', 'eq', $recordData['name']),
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertEquals(1,$search->count());

			$record = $search[0];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			foreach($recordData as $k=>$v){
				$this->assertEquals($v,$record->{$k});
			}
		}
	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindWithExpressionNe(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic(20);

		// search using expressions
		foreach($data as $recordData){
			$search = TestAsset\Basic::findAll(array(
				array('name', 'ne', $recordData['name']),
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertEquals(count($data)-1,$search->count());

			// make sure the result does not contain selected object
			foreach($search as $record){
				$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
				$this->assertNotEquals($recordData['name'],$record->name);
				$this->assertNotEquals($recordData['id'],$record->id);
			}
		}
	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindWithExpressionLike(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// search using expressions
		foreach($data as $recordData){
			$nameSuffix = substr($recordData['name'],-ceil(strlen($recordData['name'])/2));
			$search = TestAsset\Basic::findAll(array(
				array('name', 'like', '%'.$recordData['name']),
			));
			$this->assertInstanceOf("\\Zend\\Db\\ActiveRecord\\Collection",$search);
			$this->assertGreaterThanOrEqual(1,$search->count());

			// make sure the result contains our object
			$found = false;
			foreach($search as $record){
				$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
				if(
					($recordData['name'] == $record->name) &&
					($recordData['id'] == $record->id)
				){
					$found = true;
				}
			}
			$this->assertTrue($found,'The findAll() result does not contain the record we are looking for.');
		}
	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testLazyInitializing(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		$result = TestAsset\Basic::findAll();

		// check if there is any object instance stored in Registry
		foreach($data as $recordData){
			$regId = strtr(__NAMESPACE__.'\\TestAsset\\Basic','\\','_').'_'.$recordData['id'];
			$this->assertFalse(\Zend\Registry::isRegistered($regId));
		}

		// with each subsequent call to the collection iterator, a new object should be initialized
		$x = 1;
		foreach($result as $record){
			$regId = strtr(__NAMESPACE__.'\\TestAsset\\Basic','\\','_').'_'.$record->id;
			$this->assertTrue(\Zend\Registry::isRegistered($regId));

			// other records should not yet be present in registry
			for($y=$x;$y<count($data);$y++){
				$recordData = $data[$y];
				$regId = strtr(__NAMESPACE__.'\\TestAsset\\Basic','\\','_').'_'.$recordData['id'];
				$this->assertFalse(\Zend\Registry::isRegistered($regId));
			}

			$x++;
		}

	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindAllWithLimit(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();
		$limit = mt_rand(1,ceil(count($data)/2));

		$search = TestAsset\Basic::findAll(array(),array('limit'=>$limit));
		$this->assertEquals($limit,$search->count());

		// iterator count
		$x = 0;
		foreach($search as $record){
			$x++;
		}
		$this->assertEquals($limit,$x);
	}

	/**
	 * @depends testFindAll
	 * @return void
	 */
	public function testFindAllWithOrder(){
		$this->_createTableForBasic();
		$data = $this->_populateBasic();

		// sort array by name ascending
		$names = $ages = array();
		foreach ($data as $key => $row) {
			$names[$key]  = $row['name'];
			$ages[$key] = $row['age'];
		}
		$unsorted = $data;

		array_multisort($names, SORT_ASC, $data);

		// load sorted objects from database
		$search = TestAsset\Basic::findAll(array(),array(
			'order'    => 'name',
			'orderDir' => 'asc'
		));

		// test order
		$x = 0;
		foreach($search as $record){
			$recordData = $data[$x++];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			$this->assertEquals($recordData['name'],$record->name);
			$this->assertEquals($recordData['id'],$record->id);
		}

		// sort array by age ascending, then by name descending
		$data = $unsorted;
		$names = $ages = array();
		foreach ($data as $key => $row) {
			$names[$key]  = $row['name'];
			$ages[$key] = $row['age'];
		}
		
		array_multisort(
			$ages, SORT_DESC, SORT_NUMERIC,
			$names, SORT_DESC, SORT_STRING,
			$data
		);

		// load sorted objects from database
		$search = TestAsset\Basic::findAll(array(),array(
			'order' => array(
				array('age', 'desc'),
				array('name','desc')
			)
		));

		// test order
		$x = 0;
		foreach($search as $record){
			$recordData = $data[$x++];
			$this->assertInstanceOf("\\ZendTest\\Db\\ActiveRecord\\TestAsset\\Basic",$record);
			$this->assertEquals($recordData['name'],$record->name);
			$this->assertEquals($recordData['id'],$record->id);
		}

	}

	/**
	 * @depends testFindByExactMatch
	 * @return void
	 */
	public function testHasManyAndBelongsTo(){
		$this->_createTableForHasMany();

		// create parent object
		$parent = TestAsset\HasMany::factory(array(
			'name' => uniqid()
		));
		$parent->save();
		$this->assertNotEmpty($parent->id);

		// create children records directly in database
		$count = 20;
		for($x = 0;$x<$count;$x++){
			$this->_db->insert('belongstohasmany',array(
				'name' => uniqid('',true),
				'parentId' => $parent->id
			));
		}

		// fetch all children by fetching a property
		$children = $parent->children;
		$this->assertInstanceOf('\\Zend\\Db\\ActiveRecord\\Collection',$children);
		$this->assertEquals($count,$children->count());

		// fetch all children with a method call
		$children = $parent->getChildren();
		$this->assertInstanceOf('\\Zend\\Db\\ActiveRecord\\Collection',$children);
		$this->assertEquals($count,$children->count());

		// check if each children relates to its parent
		foreach($children as $child){
			$this->assertSame($parent,$child->parent);
			$this->assertSame($parent,$child->getParent());
		}
	}

	/**
	 * @depends testHasManyAndBelongsTo
	 * @return void
	 */
	public function testHasManyPersistency(){
		$this->_createTableForHasMany();

		// create parent object
		$parent = TestAsset\HasMany::factory(array(
			'name' => uniqid()
		));
		$parent->save();

		// check if ->children returns the same collection every time
		$this->assertNotEmpty($parent->id);
		$this->assertSame($parent->children, $parent->children);
		$this->assertSame($parent->children, $parent->getChildren());
		$this->assertEquals(0,$parent->children->count());
	}

	public function testSimpleSharding(){
		$this->markTestIncomplete();
	}

	public function testMasterSlaveDatabases(){
		$this->markTestIncomplete();
	}

	/**
	 * Generate random data for TestAsset\Basic class, store it in database and return it as array.
	 *
	 * @param int $maxRecords
	 * @param int $maxOccupations
	 * @param int $maxAge
	 * @return array
	 */
	protected function _populateBasic($maxRecords = 50, $maxOccupations = 10, $maxAge = 100 ){
		// generate some random data
		$data = $occupations = array();
		for($x=1; $x <= $maxOccupations;$x++){
			$occupations[uniqid()] = 0;
		}

		for($x=1 ; $x <= $maxRecords ; $x++){
			$o = array_rand($occupations,1);
			$occupations[$o]++;
			$data[] = array(
				'name' => uniqid(md5(mt_rand()),true),
				'age' => mt_rand(1,$maxAge),
				'occupation' => $o,
			);
		}

		// insert data into db table
		foreach(array_keys($data) as $k){
			$this->_db->insert('basic',$data[$k]);
			$data[$k]['id'] = $this->_db->lastInsertId('basic');
		}

		$recordCount = $this->_db->select()
								->from('basic','COUNT(*)')
								->query()
								->fetch(\Zend\Db\Db::FETCH_NUM);
		$recordCount = $recordCount[0];
		$this->assertEquals($maxRecords, $recordCount);

		return $data;
	}

	protected function _createTableForBasic(){
		return $this->_createTempTable(
			'basic',
			array(
				'id' => 'INTEGER NOT NULL AUTO_INCREMENT',
				'name' => 'VARCHAR(200)',
				'age' => 'INTEGER',
				'occupation' => 'VARCHAR(200)',
			),
			'id'
		);
	}

	protected function _createTableForNonPersistent(){
		return $this->_createTempTable(
			'nonpersistent',
			array('id' => 'INTEGER NOT NULL AUTO_INCREMENT','name' => 'VARCHAR(200)','uniqueid'=>'INTEGER'),
			'id'
		);
	}

	protected function _createTableForDynamic(){
		return $this->_createTempTable(
			'app_stats_'.date('Y'),
			array('id' => 'INTEGER NOT NULL AUTO_INCREMENT','name' => 'VARCHAR(200)','uniqueid'=>'INTEGER'),
			'id'
		);
	}

	protected function _createTableForStatic(){
		return $this->_createTempTable(
			'app_stats',
			array('id' => 'INTEGER NOT NULL AUTO_INCREMENT','name' => 'VARCHAR(200)','uniqueid'=>'INTEGER'),
			'id'
		);
	}

	protected function _createTableForHasMany(){
		$this->_createTempTable(
			'hasmany',
			array(
				'id' => 'INTEGER NOT NULL AUTO_INCREMENT',
				'name' => 'VARCHAR(200)',
			),
			'id'
		);

		$this->_createTempTable(
			'belongstohasmany',
			array(
				'id' => 'INTEGER NOT NULL AUTO_INCREMENT',
				'name' => 'VARCHAR(200)',
				'parentId' => 'INTEGER NULL'
			),
			'id'
		);


	}
	/**
	 * Create temporary db table for testing.
	 * @throws \PHPUnit_Framework_Exception
	 * @param $name
	 * @param array $spec
	 * @return void
	 */
	protected function _createTempTable($name,$spec = array(),$primaryKey =''){
		if(!$this->_db){
			throw new \PHPUnit_Framework_Exception('Cannot create db table because db is not connected!');
		}
		$e = null;
		try{
			$this->_db->describeTable($name);
		}catch(\Zend\Db\Adapter\Exception $e){}

		if($e === null){
			throw new \PHPUnit_Framework_Exception('Cannot create db table "'.$name.'", because it already exists');
		}

		$query = 'CREATE TEMPORARY TABLE '.$this->_db->quoteIdentifier($name).' ( ';
		$cols = array();
		foreach($spec as $col=>$s){
			$cols[] = $this->_db->quoteIdentifier($col).' '.$s;
		}
		$query .= join(',',$cols);

		if($primaryKey && count($cols)){
			$query .= ', PRIMARY KEY ('.$this->_db->quoteIdentifier($primaryKey).')';
		}

		 $query .= ')';



		$this->_db->query($query);

		if(!count(array_keys($this->_db->describeTable($name)))){
			throw new \PHPUnit_Framework_Exception('Could not create db table "'.$name.'"');
		}
		
		$this->_tables[] = $name;
	}

	/**
	 * @return \Zend\Db\Adapter\AbstractAdapter
	 */
	protected function _createDbAdapter(){
		$db = \Zend\Db\Db::factory(
			TESTS_ZEND_DB_ACTIVERECORD_ADAPTER,
			array(
				'host' => TESTS_ZEND_DB_ACTIVERECORD_ADAPTER_HOSTNAME,
				'port' => TESTS_ZEND_DB_ACTIVERECORD_ADAPTER_PORT,
				'username' => TESTS_ZEND_DB_ACTIVERECORD_ADAPTER_USERNAME,
				'password' => TESTS_ZEND_DB_ACTIVERECORD_ADAPTER_PASSWORD,
				'dbname' => TESTS_ZEND_DB_ACTIVERECORD_ADAPTER_DATABASE,
			)
		);
		try {
			$conn = $db->getConnection();
		} catch (\Exception $e) {
			$this->assertInstanceOf('Zend\Db\Adapter\Exception', $e,
				'Expecting Zend_Db_Adapter_Exception, got ' . get_class($e));
			$this->markTestSkipped($e->getMessage());
		}
		return $db;
	}
	

}
