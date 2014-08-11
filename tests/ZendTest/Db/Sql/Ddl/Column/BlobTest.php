<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Blob;

class BlobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * blob does not support length
     * @covers Zend\Db\Sql\Ddl\Column\Blob::setLength
     * @deprecated as of 2.4.X
     */
    public function testSetLength()
    {
        $blob = new Blob('foo', 55);
        $this->assertEquals(55, $blob->getLength());
        $this->assertSame($blob, $blob->setLength(20));
        $this->assertEquals(20, $blob->getLength());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Blob::getLength
     * @deprecated as of 2.4.X
     */
    public function testGetLength()
    {
        $blob = new Blob('foo', 55);
        $this->assertEquals(55, $blob->getLength());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Blob::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Blob('foo', 1000, true);
        $this->assertEquals(
            array(array(
                '%s %s(%s)',
                array('foo', 'BLOB', 1000),
                array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_LITERAL)
            )),
            $column->getExpressionData()
        );
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Blob::getExpressionData
     */
    public function testGetExpressionDataNotNull()
    {
        $column = new Blob('foo', 1000);
        $this->assertEquals(
            array(array(
                '%s %s(%s) %s',
                array('foo', 'BLOB', 1000, 'NOT NULL'),
                array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_LITERAL, $column::TYPE_LITERAL)
            )),
            $column->getExpressionData()
        );
    }

}
