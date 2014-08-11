<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\Db\Sql\Ddl\Column\Text::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Text('foo');
        $this->assertEquals(
            array(
                array(
                    '%s %s %s',
                    array('foo', 'TEXT', 'NOT NULL'),
                    array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL,  $column::TYPE_LITERAL)
                )
            ),
            $column->getExpressionData()
        );
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Text::getExpressionData
     */
    public function testGetExpressionDataWithLength()
    {
        $column = new Text('foo', 500);
        $this->assertEquals(
            array(
                array(
                    '%s %s(%s) %s',
                    array('foo', 'TEXT', 500, 'NOT NULL'),
                    array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_LITERAL,  $column::TYPE_LITERAL)
                )
            ),
            $column->getExpressionData()
        );
    }
}
