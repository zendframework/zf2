<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\ResultSet;

use Zend\Db\ResultSet\TreeResultSet;

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
        $current = $this->resultSet->current();
        $this->assertEquals(0, $this->resultSet->key());
        $this->assertEquals(0, $this->resultSet->getDepth());
        $this->assertEquals(0, $current['testId']);
        $this->assertEquals('zero', $current['name']);

        $this->resultSet->next();
        $current = $this->resultSet->current();
        $this->assertEquals(1, $this->resultSet->key());
        $this->assertEquals(1, $this->resultSet->getDepth());
        $this->assertEquals(1, $current['testId']);
        $this->assertEquals('one', $current['name']);

        $this->resultSet->next();
        $current = $this->resultSet->current();
        $this->assertEquals(2, $this->resultSet->key());
        $this->assertEquals(0, $this->resultSet->getDepth());
        $this->assertEquals(2, $current['testId']);
        $this->assertEquals('two', $current['name']);
    }
}
