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
		$this->_db = $this->_createDbAdapter();
		AbstractActiveRecord::setDefaultDb($this->_db);
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
	 * AbstractActiveRecord Subclasses can have their own default db adapter set
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
	 *  A global Cache adapter can be set for all ActiveRecord instances.
	 * @return void
	 */
	public function testDefaultGlobalCache(){
		$cache = \Zend\Cache\Cache::factory('Core','BlackHole');
		AbstractActiveRecord::setDefaultCache($cache);
		$this->assertSame($cache,AbstractActiveRecord::getDefaultCache());
	}

	/**
	 * AbstractActiveRecord Subclasses can have their own default Cache adapter set
	 * @return void
	 */
	public function testSubclassDefaultCache(){
		$cache1 = \Zend\Cache\Cache::factory('Core','BlackHole');
		$cache2 = \Zend\Cache\Cache::factory('Core','BlackHole');
		$this->assertNotSame($cache1,$cache2);

		AbstractActiveRecord::setDefaultCache($cache1);
		$this->assertSame($cache1,AbstractActiveRecord::getDefaultCache());

		TestAsset\Basic::setDefaultCache($cache2);
		$this->assertSame($cache1,AbstractActiveRecord::getDefaultCache());
		$this->assertSame($cache2,TestAsset\Basic::getDefaultCache());
		$this->assertNotSame($cache1,TestAsset\Basic::getDefaultCache());

		TestAsset\Basic::setDefaultCache();
	}
	
	public function testRecordInsert(){
		$this->_createTableForBasic();
		$obj = new TestAsset\Basic();
		$this->assertInstanceOf('\Zend\Db\ActiveRecord\AbstractActiveRecord',$obj);
		$obj->save();

		$this->assertEquals(1,$obj->id);
		$this->assertEquals(1,$this->_db->fetchOne('SELECT COUNT(*) FROM '.$this->_db->quoteIdentifier('basic')));
	}

	/**
	 * @depends testRecordCreation
	 * @expectedException Zend\Db\ActiveRecord\UndefinedPropertyException
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
	 * @expectedException Zend\Db\ActiveRecord\UndefinedPropertyException
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
	 * @depends testRecordCreation
	 * @return void
	 */
	public function testRecordInsertAndLoad(){
		$this->_createTableForBasic();
		$obj = new TestAsset\Basic();
		$name = mt_rand(1000,PHP_INT_MAX);
		$obj->name = $name;
		$obj->save();
	}

	/**
	 * @depends testRecordCreation
	 * @return void
	 */
	public function testPersistence(){
		$this->_createTableForBasic();
		$this->_createTableForNonPersistent();

		// create a persistent object
		$obj = new TestAsset\Basic();
		$this->assertNotEmpty($id = $obj->id);

		// try to create another instance
		$obj2 = TestAsset\Basic::findById($id);

		// both variables should point to the same object
		$this->assertSame($obj,$obj2);

		// create a non-persistent object
		$obj = new TestAsset\NonPersistent();
		$this->assertNotEmpty($id = $obj->id);

		// try to create another instance
		$obj2 = TestAsset\NonPersistent::findById($id);

		// both variables should point to the same object
		$this->assertSame($obj,$obj2);
		

	}


	protected function _createTableForBasic(){
		return $this->_createTempTable('basic',array('id' => 'INTEGER','name' => 'VARCHAR(200)'));
	}

	protected function _createTableForNonPersistent(){
		return $this->_createTempTable('nonpersistent',array('id' => 'INTEGER','name' => 'VARCHAR(200)','uniqueId'=>'INTEGER'));
	}

	/**
	 * Create temporary db table for testing.
	 * @throws \PHPUnit_Framework_Exception
	 * @param $name
	 * @param array $spec
	 * @return void
	 */
	protected function _createTempTable($name,$spec = array()){
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
		$query .= join(',',$cols) . ')';

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
			TESTS_Zend_DB_ADAPTER,
			array(
				'host' => TESTS_Zend_DB_ADAPTER_HOSTNAME,
				'port' => TESTS_Zend_DB_ADAPTER_PORT,
				'username' => TESTS_Zend_DB_ADAPTER_USERNAME,
				'password' => TESTS_Zend_DB_ADAPTER_PASSWORD,
				'dbname' => TESTS_Zend_DB_ADAPTER_DATABASE,
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
