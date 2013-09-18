<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace ZendTest\Db\ResultSet;

use Zend\Db\ResultSet\TreeResultSet;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 */
class TreeResultSetIntegrationTest extends ResultSetIntegrationTest
{
    protected function setUp()
    {
        $this->resultSet = new TreeResultSet;
    }

    public function testGetDepth()
    {
        $this->resultSet->setDepthField('testDepth');
        $this->resultSet->initialize(array(
            array('testId' => 0, 'testDepth' => 0, 'name' => 'zero'),
            array('testId' => 1, 'testDepth' => 1, 'name' => 'one'),
            array('testId' => 2, 'testDepth' => 0, 'name' => 'two'),
        ));
        
        $this->assertEquals(0, $this->resultSet->key());
        $this->assertEquals(0, $this->resultSet->getDepth());
        $this->assertEquals('zero', $this->resultSet->current()['name']);
        
        $this->resultSet->next();
        $this->assertEquals(1, $this->resultSet->key());
        $this->assertEquals(1, $this->resultSet->getDepth());
        
        $this->resultSet->next();
        $this->assertEquals(2, $this->resultSet->key());
        $this->assertEquals(0, $this->resultSet->getDepth());
    }
}
