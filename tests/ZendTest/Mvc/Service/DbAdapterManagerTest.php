<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;

use Zend\Mvc\Service\DbAdapterManager;

class DbAdapterManagerTest extends TestCase
{
    public $sampleConfig = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockPlatform = null;

    public function setUp()
    {
        $this->mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');

        $this->sampleConfig = array(
            'sqlliteDb'=>array(
                'driver'=>array(
                    'driver'=>'Pdo_Sqlite'
                ),
                'platform'=>$this->mockPlatform
            ),
            'mysqliDb'=>array(
                'driver'=>array(
                    'driver'=>'Mysqli'
                ),
                'platform'=>$this->mockPlatform
            ),
            'sqlliteDb2'=>'sqlliteDb',
            'sqlliteDb3'=>'sqlliteDb2',
            'wrongAlias'=>'dev_null',
            'wrongConfig'=>array(
                'driver'=>array()
            ),
        );

    }

    public function testAddAdapterConfig()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        foreach ( array_keys($this->sampleConfig) as $dbKey ) {
            $this->assertTrue( $dam->hasAdapterConfig($dbKey) );
        }
    }

    public function testAddAdapter()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        if (extension_loaded('pdo')) {
            $adapter = $dam->getAdapter('sqlliteDb');

            $dam->addAdapter('sqlliteDb_new', $adapter);

            $this->assertTrue( $dam->hasAdapter('sqlliteDb_new') );
            $this->assertSame( $dam->getAdapter('sqlliteDb_new'), $dam->getAdapter('sqlliteDb') );
            unset($adapter);
        }
    }

    public function testInitDbAdapter()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        if (extension_loaded('pdo')) {
            $adapter = $dam->getAdapter('sqlliteDb');
            $this->assertInstanceOf('Zend\Db\Adapter\Driver\Pdo\Pdo', $adapter->driver);

            $this->assertTrue( $dam->hasAdapter('sqlliteDb') );
            unset($adapter);
        }

        if (extension_loaded('mysqli')) {
            $adapter = $dam->getAdapter('mysqliDb');
            $this->assertInstanceOf('Zend\Db\Adapter\Driver\Mysqli\Mysqli', $adapter->driver);

            $this->assertTrue( $dam->hasAdapter('mysqliDb') );
            unset($adapter);
        }
    }

    public function testInitDbAdapterAlias()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        if (extension_loaded('pdo')) {
            $adapter1 = $dam->getAdapter('sqlliteDb');
            $adapter2 = $dam->getAdapter('sqlliteDb2');
            $adapter3 = $dam->getAdapter('sqlliteDb3');

            $this->assertSame($adapter1, $adapter2);
            $this->assertSame($adapter1, $adapter3);
            unset($adapter);
        }
    }

    /**
     * @expectedException Zend\Mvc\Service\Exception\DbAdapterManagerAdapterNotExist
     */
    public function testAdapterNotExist()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        $dam->getAdapter( 'dev_null' );
    }

    /**
     * @expectedException Zend\Mvc\Service\Exception\DbAdapterManagerAdapterCoundInit
     */
    public function testWrongAlias()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        $dam->getAdapter( 'wrongAlias' );
    }

    /**
     * @expectedException Zend\Mvc\Service\Exception\DbAdapterManagerAdapterCoundInit
     */
    public function testWrongConfig()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        $dam->getAdapter( 'wrongConfig' );
    }

    /**
     * @expectedException Zend\Mvc\Service\Exception\DbAdapterManagerAdapterAlreadyRegistered
     */
    public function testAdapterDuplicatKey()
    {
        $dam = new DbAdapterManager();
        $dam->addAdapterConfig( $this->sampleConfig );

        $adapter = $dam->getAdapter('sqlliteDb');
        $dam->addAdapter('mysqliDb',$adapter);
    }
}
