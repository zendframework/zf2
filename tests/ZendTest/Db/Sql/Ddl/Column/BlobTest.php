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
     * @deprecated
     */
    public function testSetLength()
    {
        return;
        $blob = new Blob('foo', 55);
        $this->assertEquals(55, $blob->getLength());
        $this->assertSame($blob, $blob->setLength(20));
        $this->assertEquals(20, $blob->getLength());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Blob::getLength
     * @deprecated
     */
    public function testGetLength()
    {
        return;
        $blob = new Blob('foo', 55);
        $this->assertEquals(55, $blob->getLength());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Blob::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Blob('foo', true);
        $this->assertEquals(
            array(array('%s %s', array('foo', 'BLOB'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }

}
