<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Platform;

use Zend\Db\Sql\Platform\AbstractPlatform;

class AbstractPlatformTest extends \PHPUnit_Framework_TestCase
{
    protected $platform = null;

    protected function setUp()
    {
        $this->platform = new AbstractPlatform();
    }

    public function testSetSubject()
    {
        $subject = new \stdClass();
        $this->platform->setSubject($subject);
    }

    public function testGetDecorators()
    {
        $this->assertSame($this->platform->getDecorators(), array(
            'mysql'     => array(
                'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\Mysql\SelectDecorator',
                'Zend\Db\Sql\Ddl\CreateTable' => 'Zend\Db\Sql\Platform\Mysql\Ddl\CreateTableDecorator',
            ),
            'sqlserver' => array(
                'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\SqlServer\SelectDecorator',
            ),
            'oracle'    => array(
                'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\Oracle\SelectDecorator',
            ),
        ));
    }

    public function testSetTypeDecorator()
    {
        $this->platform->setTypeDecorator('someType', 'someDecorator');
        $decorators = $this->platform->getDecorators();
        $this->assertTrue(isset($decorators['sql92']['someType']));
        $this->assertEquals($decorators['sql92']['someType'], 'someDecorator');

        $this->platform->setTypeDecorator('someType', 'someDecorator', 'somePlatform');
        $decorators = $this->platform->getDecorators();
        $this->assertTrue(isset($decorators['someplatform']['someType']));
        $this->assertEquals($decorators['someplatform']['someType'], 'someDecorator');
    }

    public function testResolvePlatform()
    {
        $resolvePlatform = new \ReflectionMethod($this->platform, 'resolvePlatform');
        $resolvePlatform->setAccessible(true);

        // resolve dafault
        $platform = $resolvePlatform->invoke($this->platform, null);
        $this->assertInstanceOf('Zend\Db\Adapter\Platform\Sql92', $platform);

        // resolve platform
        $adapterPlatform = new \ZendTest\Db\TestAsset\TrustingMySqlPlatform;
        $this->assertSame($adapterPlatform, $resolvePlatform->invoke($this->platform, $adapterPlatform));

        // resolve from adapter
        $adapter = new \Zend\Db\Adapter\Adapter(array('driver'=>'mysqli'), $adapterPlatform);
        $platform = $resolvePlatform->invoke($this->platform, $adapter);
        $this->assertSame($adapterPlatform, $platform);
    }

    public function testGetDecorator()
    {
        $subject = new \Zend\Db\Sql\Select();
        $this->platform->setSubject($subject);

        $method = new \ReflectionMethod($this->platform, 'getDecorator');
        $method->setAccessible(true);
        $decorator = $method->invoke($this->platform, new \ZendTest\Db\TestAsset\TrustingMySqlPlatform);
        $this->assertInstanceOf('Zend\Db\Sql\Platform\Mysql\SelectDecorator', $decorator);
    }


}
