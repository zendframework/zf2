<?php
namespace ZendTest\Db\Adapter\Driver\Mysqli;

/*
 * 
 * VERSION NOTE: Partial scaffold, taken from Zend\Db\Adapter\AdapterTest
 * 
 */

use Zend\Db\Adapter\Driver\Mysqli\Result,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\ResultSet\Row;

class ResultTest extends \PHPUnit_Framework_TestCase
{
	
	//Until the mysqli mockup has been created
	const MYSQLI_HOST     = 'localhost';
	const MYSQLI_USER     = 'username';
	const MYSQLI_PASSWORD = 'password';
	const MYSQLI_DATABASE = 'database';
	
	
	/**
	 * @var \mysqli_stmt
	 */
	protected $mockMysqliStmt = null;

	/**
	 * @var string
	 */
	protected $randTableName = null;
	

    /**
     * Creates a temporary table and return a fake mysqli_stmt
     * 
     * @return mysqli_stmt
     */
    protected function getMysqliStmt()
    {
    	$mysqli = new \mysqli(MYSQLI_HOST, MYSQLI_USER, MYSQLI_PASSWORD, MYSQLI_DATABASE);
    	
    	$this->randTableName = 'ResultTest'.rand(1000000000,9999999999);
    	
    	$mysqli->query("CREATE TEMPORARY TABLE {$this->randTableName} (id int, field_1 tinyblob, field_2 tinyblob)");  
    	
    	$mysqli->query('INSERT INTO ' . $this->randTableName . ' VALUES 
    						(1, "field1_1", "field2_1"),
    						(2, "field1_2", "field2_2"),
    						(3, "field1_3", "field2_3"),
    						(4, "field1_4", "field2_4"),
    						(5, "field1_5", "field2_5")
    					');
    	
    	//Not affected by any existing database or table
    	return $mysql->prepare("SELECT * FROM {$randTableName}");
    }
    
    
    protected function setUp()
    {
    	
    	$this->mockMysqli      = $this->getMysqliStmt();
    	
    }

    /**
     * @testdox unit test: Test testIterator() will check the Iterator interface compliance
     * @covers Zend\Db\Adapter\Mysqli\Result::initialize()
     * @covers Zend\Db\ResultSet\ResultSet::setDataSource()
     * @covers Zend\Db\ResultSet\ResultSet::rewind()
     * @covers Zend\Db\ResultSet\ResultSet::current()
     * @covers Zend\Db\ResultSet\ResultSet::next()
     * @covers Zend\Db\ResultSet\ResultSet::valid()
     * @covers Zend\Db\ResultSet\ResultSet::key()
     * @covers Zend\Db\ResultSet\ResultSet::count()
     */
    public function testIterator()
    {

    	$mockMysqli = clone $this->mockMysqliStmt;
    	
    	$result     = new Result();
    	$result->initialize($mockMysqli, null);
    	
    	$resultSet  = new ResultSet();
    	$resultSet->setDataSource($result);
    	 
    	$resultSet->rewind(); //1
    	$this->assertTrue($resultSet->valid());
    	$this->checkRow($resultSet->current(), 1);
    	$this->assertEqual(0, $resultSet->key()); //id-1
    	
    	//Check row count
    	$this->assertEqual(5, $resultSet->count());
    	
    	$resultSet->next(); //2
    	$this->assertTrue($resultSet->valid());
    	$this->checkRow($resultSet->current(), 2);
    	$this->assertEqual(1, $resultSet->key()); //id-1

    	//Checks multiple nexts in a row
    	$resultSet->next(); //3
    	$resultSet->next(); //4
    	$resultSet->next(); //5
    	$this->assertTrue($resultSet->valid());
    	$this->checkRow($resultSet->current(), 5);
    	$this->assertEqual(4, $resultSet->key()); //id-1
    	
    	//Check rewind
    	$resultSet->rewind(); //1
    	$this->assertTrue($resultSet->valid());
    	$this->checkRow($resultSet->current(), 1);
    	$this->assertEqual(0, $resultSet->key()); //id-1
    	
    	//Checks multiple nexts in a row
    	$resultSet->next(); //2
    	$resultSet->next(); //3
    	$this->assertTrue($resultSet->valid());
    	$this->checkRow($resultSet->current(), 3);
    	$this->assertEqual(2, $resultSet->key()); //id-1

    	//Check row count - should be the same after rewind()
    	$this->assertEqual(5, $resultSet->count());

    	//Assert last line as false
    	$resultSet->rewind(); //1
    	$resultSet->next(); //2
    	$resultSet->next(); //3
    	$resultSet->next(); //4
    	$resultSet->next(); //5
    	$resultSet->next(); //false (end of rows)
    	$this->assertFalse($resultSet->valid());
    	$this->assertFalse($resultSet->current());
    	$this->assertNull($resultSet->key());
    	
    	$resultSet->next(); //false (end of rows)
    	$this->assertFalse($resultSet->valid());
    	$this->assertFalse($resultSet->current());
    	$this->assertEqual(5, $resultSet->key());
    	$this->assertNull($resultSet->key());
    	
    	foreach($resultSet as $key => $row) {
    		$this->checkRow($key+1 , $row); //$key = id-1
    	}
    	
    }
    
    /**
     * Performs the due checks on a single row
     * 
     * @param int $id
     * @param mixed $row
     */
    protected function checkRow($id, $row)
    {
    	$this->assertInstanceOf('Zend\Db\ResultSet\Row', $row);
    	$this->assertEquals($id, $row->id);
    	$this->assertEquals('field1_'.$id, $row->field_1);
    	$this->assertEquals('field2_'.$id, $row->field_2);
    }
    

}
