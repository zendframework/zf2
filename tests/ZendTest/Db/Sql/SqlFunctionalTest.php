<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql;

use Zend\Db\Adapter;
use Zend\Db\Sql;
use ZendTest\Db\TestAsset;

/**
 * @method \Zend\Db\Sql\Select select(null|string $table)
 * @method \Zend\Db\Sql\Update update(null|string $table)
 * @method \Zend\Db\Sql\Delete delete(null|string $table)
 * @method \Zend\Db\Sql\Insert insert(null|string $table)
 * @method \Zend\Db\Sql\Ddl\CreateTable createTable(null|string $table)
 * @method \Zend\Db\Sql\Ddl\Column\Column createColumn(null|string $name)
 */
class SqlFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected function dataProvider_CommonProcessMethods()
    {
        return array(
            'Select::processOffset()' => array(
                'sqlObject' => $this->select('foo')->offset(10),
                'expected'  => array(
                    'sql92' => array(
                        'string'     => 'SELECT "foo".* FROM "foo" OFFSET \'10\'',
                        'prepare'    => 'SELECT "foo".* FROM "foo" OFFSET ?',
                        'parameters' => array('offset' => 10),
                    ),
                    'MySql' => array(
                        'string'     => 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET 10',
                        'prepare'    => 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET ?',
                        'parameters' => array('offset' => 10),
                    ),
                    'Oracle' => array(
                        'string'     => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b ) WHERE b_rownum > (10)',
                        'prepare'    => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b ) WHERE b_rownum > (:offset)',
                        'parameters' => array('offset' => 10),
                    ),
                    'SqlServer' => array(
                        'string'     => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__ZEND_ROW_NUMBER] FROM [foo] ) AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN 10+1 AND 0+10',
                        'prepare'    => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__ZEND_ROW_NUMBER] FROM [foo] ) AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN ?+1 AND ?+?',
                        'parameters' => array('offset' => 10, 'limit' => null, 'offsetForSum' => 10),
                    ),
                ),
            ),
            'Select::processLimit()' => array(
                'sqlObject' => $this->select('foo')->limit(10),
                'expected'  => array(
                    'sql92' => array(
                        'string'     => 'SELECT "foo".* FROM "foo" LIMIT \'10\'',
                        'prepare'    => 'SELECT "foo".* FROM "foo" LIMIT ?',
                        'parameters' => array('limit' => 10),
                    ),
                    'MySql' => array(
                        'string'     => 'SELECT `foo`.* FROM `foo` LIMIT 10',
                        'prepare'    => 'SELECT `foo`.* FROM `foo` LIMIT ?',
                        'parameters' => array('limit' => 10),
                    ),
                    'Oracle' => array(
                        'string'     => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (0+10)) WHERE b_rownum >= (0 + 1)',
                        'prepare'    => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (:offset+:limit)) WHERE b_rownum >= (:offset + 1)',
                        'parameters' => array('offset' => 0, 'limit' => 10),
                    ),
                    'SqlServer' => array(
                        'string'     => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__ZEND_ROW_NUMBER] FROM [foo] ) AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN 0+1 AND 10+0',
                        'prepare'    => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__ZEND_ROW_NUMBER] FROM [foo] ) AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN ?+1 AND ?+?',
                        'parameters' => array('offset' => null, 'limit' => 10, 'offsetForSum' => null),
                    ),
                ),
            ),
            'Select::processLimitOffset()' => array(
                'sqlObject' => $this->select('foo')->limit(10)->offset(5),
                'expected'  => array(
                    'sql92' => array(
                        'string'     => 'SELECT "foo".* FROM "foo" LIMIT \'10\' OFFSET \'5\'',
                        'prepare'    => 'SELECT "foo".* FROM "foo" LIMIT ? OFFSET ?',
                        'parameters' => array('limit' => 10, 'offset' => 5),
                    ),
                    'MySql' => array(
                        'string'     => 'SELECT `foo`.* FROM `foo` LIMIT 10 OFFSET 5',
                        'prepare'    => 'SELECT `foo`.* FROM `foo` LIMIT ? OFFSET ?',
                        'parameters' => array('limit' => 10, 'offset' => 5),
                    ),
                    'Oracle' => array(
                        'string'     => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (5+10)) WHERE b_rownum >= (5 + 1)',
                        'prepare'    => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (:offset+:limit)) WHERE b_rownum >= (:offset + 1)',
                        'parameters' => array('offset' => 5, 'limit' => 10),
                    ),
                    'SqlServer' => array(
                        'string'     => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__ZEND_ROW_NUMBER] FROM [foo] ) AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN 5+1 AND 10+5',
                        'prepare'    => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__ZEND_ROW_NUMBER] FROM [foo] ) AS [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [ZEND_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__ZEND_ROW_NUMBER] BETWEEN ?+1 AND ?+?',
                        'parameters' => array('offset' => 5, 'limit' => 10, 'offsetForSum' => 5),
                    ),
                ),
            ),
            'Select::processJoin()' => array(
                'sqlObject' => $this->select('a')->join(array('b'=>$this->select('c')->where(array('cc'=>10))), 'd=e')->where(array('x'=>20)),
                'expected'  => array(
                    'sql92' => array(
                        'string'     => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = \'10\') AS "b" ON "d"="e" WHERE "x" = \'20\'',
                        'prepare'    => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = ?) AS "b" ON "d"="e" WHERE "x" = ?',
                        'parameters' => array('subselect1where1'=>10, 'where1'=>20),
                    ),
                    'MySql' => array(
                        'string'     => 'SELECT `a`.*, `b`.* FROM `a` INNER JOIN (SELECT `c`.* FROM `c` WHERE `cc` = \'10\') AS `b` ON `d`=`e` WHERE `x` = \'20\'',
                        'prepare'    => 'SELECT `a`.*, `b`.* FROM `a` INNER JOIN (SELECT `c`.* FROM `c` WHERE `cc` = ?) AS `b` ON `d`=`e` WHERE `x` = ?',
                        'parameters' => array('subselect2where1'=>10, 'where2'=>20),
                    ),
                    'Oracle' => array(
                        'string'     => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = \'10\') "b" ON "d"="e" WHERE "x" = \'20\'',
                        'prepare'    => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = ?) "b" ON "d"="e" WHERE "x" = ?',
                        'parameters' => array('subselect2where1'=>10, 'where2'=>20),
                    ),
                    'SqlServer' => array(
                        'string'     => 'SELECT [a].*, [b].* FROM [a] INNER JOIN (SELECT [c].* FROM [c] WHERE [cc] = \'10\') AS [b] ON [d]=[e] WHERE [x] = \'20\'',
                        'prepare'    => 'SELECT [a].*, [b].* FROM [a] INNER JOIN (SELECT [c].* FROM [c] WHERE [cc] = ?) AS [b] ON [d]=[e] WHERE [x] = ?',
                        'parameters' => array('subselect2where1'=>10, 'where2'=>20),
                    ),
                ),
            ),
            'Ddl::CreateTable::processColumns()' => array(
                'sqlObject' => $this->createTable('foo')
                                    ->addColumn($this->createColumn('col1')->setOption('identity', true)->setOption('comment', 'Comment1'))
                                    ->addColumn($this->createColumn('col2')->setOption('identity', true)->setOption('comment', 'Comment2')),
                'expected'  => array(
                    'sql92'     => "CREATE TABLE \"foo\" ( \n    \"col1\" INTEGER NOT NULL,\n    \"col2\" INTEGER NOT NULL \n)",
                    'MySql'     => "CREATE TABLE `foo` ( \n    `col1` INTEGER NOT NULL AUTO_INCREMENT COMMENT 'Comment1',\n    `col2` INTEGER NOT NULL AUTO_INCREMENT COMMENT 'Comment2' \n)",
                    'Oracle'    => "CREATE TABLE \"foo\" ( \n    \"col1\" INTEGER NOT NULL,\n    \"col2\" INTEGER NOT NULL \n)",
                    'SqlServer' => "CREATE TABLE [foo] ( \n    [col1] INTEGER NOT NULL,\n    [col2] INTEGER NOT NULL \n)",
                ),
            ),
            'Ddl::CreateTable::processTable()' => array(
                'sqlObject' => $this->createTable('foo')->setTemporary(true),
                'expected'  => array(
                    'sql92'     => "CREATE TEMPORARY TABLE \"foo\" ( \n)",
                    'MySql'     => "CREATE TEMPORARY TABLE `foo` ( \n)",
                    'Oracle'    => "CREATE TEMPORARY TABLE \"foo\" ( \n)",
                    'SqlServer' => "CREATE TABLE [#foo] ( \n)",
                ),
            ),
            'Select::processSubSelect()' => array(
                'sqlObject' => $this->select(array('a' => $this->select(array('b' => $this->select('c')->where(array('cc'=>'CC'))))->where(array('bb'=>'BB'))))->where(array('aa'=>'AA')),
                'expected'  => array(
                    'sql92' => array(
                        'string'     => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = \'CC\') AS "b" WHERE "bb" = \'BB\') AS "a" WHERE "aa" = \'AA\'',
                        'prepare'    => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = ?) AS "b" WHERE "bb" = ?) AS "a" WHERE "aa" = ?',
                        'parameters' => array('subselect2where1' => 'CC', 'subselect1where1' => 'BB', 'where1' => 'AA'),
                    ),
                    'MySql' => array(
                        'string'     => 'SELECT `a`.* FROM (SELECT `b`.* FROM (SELECT `c`.* FROM `c` WHERE `cc` = \'CC\') AS `b` WHERE `bb` = \'BB\') AS `a` WHERE `aa` = \'AA\'',
                        'prepare'    => 'SELECT `a`.* FROM (SELECT `b`.* FROM (SELECT `c`.* FROM `c` WHERE `cc` = ?) AS `b` WHERE `bb` = ?) AS `a` WHERE `aa` = ?',
                        'parameters' => array('subselect4where1' => 'CC', 'subselect3where1' => 'BB', 'where2' => 'AA'),
                    ),
                    'Oracle' => array(
                        'string'     => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = \'CC\') "b" WHERE "bb" = \'BB\') "a" WHERE "aa" = \'AA\'',
                        'prepare'    => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = ?) "b" WHERE "bb" = ?) "a" WHERE "aa" = ?',
                        'parameters' => array('subselect4where1' => 'CC', 'subselect3where1' => 'BB', 'where2' => 'AA'),
                    ),
                    'SqlServer' => array(
                        'string'     => 'SELECT [a].* FROM (SELECT [b].* FROM (SELECT [c].* FROM [c] WHERE [cc] = \'CC\') AS [b] WHERE [bb] = \'BB\') AS [a] WHERE [aa] = \'AA\'',
                        'prepare'    => 'SELECT [a].* FROM (SELECT [b].* FROM (SELECT [c].* FROM [c] WHERE [cc] = ?) AS [b] WHERE [bb] = ?) AS [a] WHERE [aa] = ?',
                        'parameters' => array('subselect4where1' => 'CC', 'subselect3where1' => 'BB', 'where2' => 'AA'),
                    ),
                ),
            ),
            'Delete::processSubSelect()' => array(
                'sqlObject' => $this->delete('foo')->where(array('x'=>$this->select('foo')->where(array('x'=>'y')))),
                'expected'  => array(
                    'sql92'     => array(
                        'string'     => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => array('subselect1where1' => 'y'),
                    ),
                    'MySql'     => array(
                        'string'     => 'DELETE FROM `foo` WHERE `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = \'y\')',
                        'prepare'    => 'DELETE FROM `foo` WHERE `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = ?)',
                        'parameters' => array('subselect2where1' => 'y'),
                    ),
                    'Oracle'    => array(
                        'string'     => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => array('subselect3where1' => 'y'),
                    ),
                    'SqlServer' => array(
                        'string'     => 'DELETE FROM [foo] WHERE [x] = (SELECT [foo].* FROM [foo] WHERE [x] = \'y\')',
                        'prepare'    => 'DELETE FROM [foo] WHERE [x] = (SELECT [foo].* FROM [foo] WHERE [x] = ?)',
                        'parameters' => array('subselect4where1' => 'y'),
                    ),
                ),
            ),
            'Update::processSubSelect()' => array(
                'sqlObject' => $this->update('foo')->set(array('x'=>$this->select('foo'))),
                'expected'  => array(
                    'sql92'     => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo")',
                    'MySql'     => 'UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo`)',
                    'Oracle'    => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo")',
                    'SqlServer' => 'UPDATE [foo] SET [x] = (SELECT [foo].* FROM [foo])',
                ),
            ),
            'Insert::processSubSelect()' => array(
                'sqlObject' => $this->insert('foo')->select($this->select('foo')->where(array('x'=>'y'))),
                'expected'  => array(
                    'sql92'     => array(
                        'string'     => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = \'y\'',
                        'prepare'    => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = ?',
                        'parameters' => array('subselect1where1' => 'y'),
                    ),
                    'MySql'     => array(
                        'string'     => 'INSERT INTO `foo`  SELECT `foo`.* FROM `foo` WHERE `x` = \'y\'',
                        'prepare'    => 'INSERT INTO `foo`  SELECT `foo`.* FROM `foo` WHERE `x` = ?',
                        'parameters' => array('subselect2where1' => 'y'),
                    ),
                    'Oracle'    => array(
                        'string'     => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = \'y\'',
                        'prepare'    => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = ?',
                        'parameters' => array('subselect3where1' => 'y'),
                    ),
                    'SqlServer' => array(
                        'string'     => 'INSERT INTO [foo]  SELECT [foo].* FROM [foo] WHERE [x] = \'y\'',
                        'prepare'    => 'INSERT INTO [foo]  SELECT [foo].* FROM [foo] WHERE [x] = ?',
                        'parameters' => array('subselect4where1' => 'y'),
                    ),
                ),
            ),
            'Update::processExpression()' => array(
                'sqlObject' => $this->update('foo')->set(array('x'=>new Sql\Expression('?', array($this->select('foo')->where(array('x'=>'y')))))),
                'expected'  => array(
                    'sql92'     => array(
                        'string'     => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => array('subselect1where1' => 'y'),
                    ),
                    'MySql'     => array(
                        'string'     => 'UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = \'y\')',
                        'prepare'    => 'UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = ?)',
                        'parameters' => array('subselect2where1' => 'y'),
                    ),
                    'Oracle'    => array(
                        'string'     => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => array('subselect3where1' => 'y'),
                    ),
                    'SqlServer' => array(
                        'string'     => 'UPDATE [foo] SET [x] = (SELECT [foo].* FROM [foo] WHERE [x] = \'y\')',
                        'prepare'    => 'UPDATE [foo] SET [x] = (SELECT [foo].* FROM [foo] WHERE [x] = ?)',
                        'parameters' => array('subselect4where1' => 'y'),
                    ),
                ),
            ),
        );
    }

    protected function dataProvider_Decorators()
    {
        return array(
            'RootDecorators::Select' => array(
                'sqlObject' => $this->select('foo')->where(array('x'=>$this->select('bar'))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Select' => new TestAsset\SelectDecorator,
                        ),
                        'string' => 'SELECT "foo".* FROM "foo" WHERE "x" = (SELECT "bar".* FROM "bar")',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Select' => new TestAsset\SelectDecorator,
                        ),
                        'string' => 'SELECT `foo`.* FROM `foo` WHERE `x` = (SELECT `bar`.* FROM `bar`)',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Select' => new TestAsset\SelectDecorator,
                        ),
                        'string' => 'SELECT "foo".* FROM "foo" WHERE "x" = (SELECT "bar".* FROM "bar")',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Select' => new TestAsset\SelectDecorator,
                        ),
                        'string' => 'SELECT [foo].* FROM [foo] WHERE [x] = (SELECT [bar].* FROM [bar])',
                    ),
                ),
            ),
            /* TODO - should be implemeted
            'RootDecorators::Insert' => array(
                'sqlObject' => $this->insert('foo')->select($this->select()),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string' => 'INSERT INTO "foo"  {=SELECT_Sql92=}',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string' => 'INSERT INTO `foo`  {=SELECT_MySql=}',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string' => 'INSERT INTO "foo"  {=SELECT_Oracle=}',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string' => 'INSERT INTO [foo]  {=SELECT_SqlServer=}',
                    ),
                ),
            ),
            'RootDecorators::Delete' => array(
                'sqlObject' => $this->delete('foo')->where(array('x'=>$this->select('foo'))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string' => 'DELETE FROM "foo" WHERE "x" = ({=SELECT_Sql92=})',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string' => 'DELETE FROM `foo` WHERE `x` = ({=SELECT_MySql=})',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string' => 'DELETE FROM "foo" WHERE "x" = ({=SELECT_Oracle=})',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string' => 'DELETE FROM [foo] WHERE [x] = ({=SELECT_SqlServer=})',
                    ),
                ),
            ),
            'RootDecorators::Update' => array(
                'sqlObject' => $this->update('foo')->where(array('x'=>$this->select('foo'))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string' => 'UPDATE "foo" SET  WHERE "x" = ({=SELECT_Sql92=})',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string' => 'UPDATE `foo` SET  WHERE `x` = ({=SELECT_MySql=})',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string' => 'UPDATE "foo" SET  WHERE "x" = ({=SELECT_Oracle=})',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Zend\Db\Sql\Select' => array('Zend\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string' => 'UPDATE [foo] SET  WHERE [x] = ({=SELECT_SqlServer=})',
                    ),
                ),
            ),
            'DecorableExpression()' => array(
                'sqlObject' => $this->update('foo')->where(array('x'=>new Sql\Expression('?', array($this->select('foo'))))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Zend\Db\Sql\Select'     => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string'     => 'UPDATE "foo" SET  WHERE "x" = {decorate-({=SELECT_Sql92=})-decorate}',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Zend\Db\Sql\Select'     => array('Zend\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string'     => 'UPDATE `foo` SET  WHERE `x` = {decorate-({=SELECT_MySql=})-decorate}',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Zend\Db\Sql\Select'     => array('Zend\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string'     => 'UPDATE "foo" SET  WHERE "x" = {decorate-({=SELECT_Oracle=})-decorate}',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Zend\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Zend\Db\Sql\Select'     => array('Zend\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string'     => 'UPDATE [foo] SET  WHERE [x] = {decorate-({=SELECT_SqlServer=})-decorate}',
                    ),
                ),
            ),*/
        );
    }

    public function dataProvider()
    {
        $data = array_merge(
            $this->dataProvider_CommonProcessMethods(),
            $this->dataProvider_Decorators()
        );

        $res = array();
        foreach ($data as $index => $test) {
            foreach ($test['expected'] as $platform => $expected) {
                $res[$index . '->' . $platform] = array(
                    'sqlObject' => $test['sqlObject'],
                    'platform'  => $platform,
                    'expected'  => $expected,
                );
            }
        }
        return $res;
    }

    /**
     * @param type $sqlObject
     * @param type $platform
     * @param type $expected
     * @dataProvider dataProvider
     */
    public function test($sqlObject, $platform, $expected)
    {
        $sql = new Sql\Sql($this->resolveAdapter($platform));

        if (is_array($expected) && isset($expected['decorators'])) {
            foreach ($expected['decorators'] as $type=>$decorator) {
                $sql->getSqlPlatform()->setTypeDecorator($type, $this->resolveDecorator($decorator));
            }
        }

        $expectedString = is_string($expected) ? $expected : (isset($expected['string']) ? $expected['string'] : null);
        if ($expectedString) {
            $actual = $sql->getSqlStringForSqlObject($sqlObject);
            $this->assertEquals($expectedString, $actual, "getSqlString()");
        }
        if (is_array($expected) && isset($expected['prepare'])) {
            $actual = $sql->prepareStatementForSqlObject($sqlObject);
            $this->assertEquals($expected['prepare'], $actual->getSql(), "prepareStatement()");
            if (isset($expected['parameters'])) {
                $actual = $actual->getParameterContainer()->getNamedArray();
                $this->assertSame($expected['parameters'], $actual, "parameterContainer()");
            }
        }
    }

    protected function resolveDecorator($decorator)
    {
        if (is_array($decorator)) {
            $decoratorMock = $this->getMock($decorator[0], array('buildSqlString'), array(null));
            $decoratorMock->expects($this->any())->method('buildSqlString')->will($this->returnValue($decorator[1]));
            return $decoratorMock;
        }
        if ($decorator instanceof Sql\Platform\PlatformDecoratorInterface) {
            return $decorator;
        }
        return;
    }

    protected function resolveAdapter($platform)
    {
        switch ($platform) {
            case 'sql92'     : $platform  = new TestAsset\TrustingSql92Platform();     break;
            case 'MySql'     : $platform  = new TestAsset\TrustingMysqlPlatform();     break;
            case 'Oracle'    : $platform  = new TestAsset\TrustingOraclePlatform();    break;
            case 'SqlServer' : $platform  = new TestAsset\TrustingSqlServerPlatform(); break;
            default : $platform = null;
        }

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnCallback(function () {return new Adapter\StatementContainer;}));

        return new Adapter\Adapter($mockDriver, $platform);
    }

    public function __call($name, $arguments)
    {
        $arg0 = isset($arguments[0]) ? $arguments[0] : null;
        switch ($name) {
            case 'select'       : return new Sql\Select($arg0);
            case 'delete'       : return new Sql\Delete($arg0);
            case 'update'       : return new Sql\Update($arg0);
            case 'insert'       : return new Sql\Insert($arg0);
            case 'createTable'  : return new Sql\Ddl\CreateTable($arg0);
            case 'createColumn' : return new Sql\Ddl\Column\Column($arg0);
        }
    }
}
