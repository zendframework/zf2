<?php

namespace ZendText\Db\Adapter\Driver\Pgsql\Feature;

use PHPUnit_Framework_TestCase;
use Zend\Db\Adapter\Driver\Pgsql\Feature\SequenceHelper;

class SequenceHelperTest extends PHPUnit_Framework_TestCase
{
    protected $mockDriver;
    protected $mockPlatform;
    protected $mockAdapter;

    protected function setUp()
    {
        $this->mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');

        $this->mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $this->mockPlatform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('PostgreSQL'));

        $this->mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($this->mockDriver, $this->mockPlatform));
    }

    public function testPostInitChecksPlatformName()
    {
        $mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $mockPlatform->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Foo'));

        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($this->mockDriver, $mockPlatform));

        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');
        $tgReflection = new \ReflectionClass('Zend\Db\TableGateway\AbstractTableGateway');
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            if ('adapter' == $tgPropReflection->getName()) {
                $tgPropReflection->setValue($tableGatewayMock, $mockAdapter);
            }
        }

        $feature = new SequenceHelper();
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        $this->assertEquals(null, $tableGatewayMock->getSequence());
    }

    public function testPostInitWithoutMetadataFeature()
    {
        $featureSetMock = $this->getMock('Zend\Db\TableGateway\Feature\FeatureSet');
        $featureSetMock->expects($this->any())
            ->method('getFeatureByClassName')
            ->with($this->equalTo('Zend\Db\TableGateway\Feature\MetadataFeature'))
            ->will($this->returnValue('false'));

        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');
        $tgReflection = new \ReflectionClass('Zend\Db\TableGateway\AbstractTableGateway');
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'adapter':
                    $tgPropReflection->setValue($tableGatewayMock, $this->mockAdapter);
                    break;
                case 'featureSet':
                    $tgPropReflection->setValue($tableGatewayMock, $featureSetMock);
                    break;
            }
        }

        $feature = new SequenceHelper();
        $feature->setTableGateway($tableGatewayMock);

        $exThrow = false;
        try {
            $feature->postInitialize();
        } catch(\Zend\Db\Adapter\Exception\RuntimeException $e) {
            $exThrow = true;
        }

        if (!$exThrow) {
            $this->fail('No exception thrown while metadata feature was not registered');
        }

        $this->assertEquals(null, $tableGatewayMock->getSequence());
    }

    public function testPostInitWithoutPk()
    {
        $metadataFeatureMock = $this->getMock('Zend\Db\TableGateway\Feature\MetadataFeature');

        $featureSetMock = $this->getMock('Zend\Db\TableGateway\Feature\FeatureSet');
        $featureSetMock->expects($this->any())
            ->method('getFeatureByClassName')
            ->with($this->equalTo('Zend\Db\TableGateway\Feature\MetadataFeature'))
            ->will($this->returnValue($metadataFeatureMock));

        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');
        $tgReflection = new \ReflectionClass('Zend\Db\TableGateway\AbstractTableGateway');
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'adapter':
                    $tgPropReflection->setValue($tableGatewayMock, $this->mockAdapter);
                    break;
                case 'featureSet':
                    $tgPropReflection->setValue($tableGatewayMock, $featureSetMock);
                    break;
            }
        }

        $feature = new SequenceHelper();
        $feature->setTableGateway($tableGatewayMock);

        $exThrow = false;
        try {
            $feature->postInitialize();
        } catch(\Zend\Db\Adapter\Exception\RuntimeException $e) {
            $exThrow = true;
        }

        if (!$exThrow) {
            $this->fail('No exception thrown while metadata feature was not registered');
        }

        $this->assertEquals(null, $tableGatewayMock->getSequence());
    }

    public function testPostInitWithComplexPk()
    {
        $metadataFeatureMock = $this->getMock('Zend\Db\TableGateway\Feature\MetadataFeature');
        $r = new \ReflectionClass('Zend\Db\TableGateway\Feature\MetadataFeature');
        foreach($r->getProperties() as $prop) {
            $prop->setAccessible(true);
            if ($prop->getName() == 'sharedData') {
                $prop->setValue($metadataFeatureMock, array('metadata' => array('primaryKey' => array('foo', 'bar'))));
            }
        }

        $featureSetMock = $this->getMock('Zend\Db\TableGateway\Feature\FeatureSet');
        $featureSetMock->expects($this->any())
            ->method('getFeatureByClassName')
            ->with($this->equalTo('Zend\Db\TableGateway\Feature\MetadataFeature'))
            ->will($this->returnValue($metadataFeatureMock));

        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');
        $tgReflection = new \ReflectionClass('Zend\Db\TableGateway\AbstractTableGateway');
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'adapter':
                    $tgPropReflection->setValue($tableGatewayMock, $this->mockAdapter);
                    break;
                case 'featureSet':
                    $tgPropReflection->setValue($tableGatewayMock, $featureSetMock);
                    break;
            }
        }

        $feature = new SequenceHelper();
        $feature->setTableGateway($tableGatewayMock);

        $exThrow = false;
        try {
            $feature->postInitialize();
        } catch(\Zend\Db\Adapter\Exception\RuntimeException $e) {
            $exThrow = true;
        }

        if (!$exThrow) {
            $this->fail('No exception thrown while metadata feature was not registered');
        }

        $this->assertEquals(null, $tableGatewayMock->getSequence());
    }

    public function testPostInitSetsSequence()
    {
        $metadataFeatureMock = $this->getMock('Zend\Db\TableGateway\Feature\MetadataFeature');
        $r = new \ReflectionClass('Zend\Db\TableGateway\Feature\MetadataFeature');
        foreach($r->getProperties() as $prop) {
            $prop->setAccessible(true);
            if ($prop->getName() == 'sharedData') {
                $prop->setValue($metadataFeatureMock, array('metadata' => array('primaryKey' => 'bar')));
            }
        }

        $featureSetMock = $this->getMock('Zend\Db\TableGateway\Feature\FeatureSet');
        $featureSetMock->expects($this->any())
            ->method('getFeatureByClassName')
            ->with($this->equalTo('Zend\Db\TableGateway\Feature\MetadataFeature'))
            ->will($this->returnValue($metadataFeatureMock));

        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');
        $tgReflection = new \ReflectionClass('Zend\Db\TableGateway\AbstractTableGateway');
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'table':
                    $tgPropReflection->setValue($tableGatewayMock, 'foo');
                    break;
                case 'adapter':
                    $tgPropReflection->setValue($tableGatewayMock, $this->mockAdapter);
                    break;
                case 'featureSet':
                    $tgPropReflection->setValue($tableGatewayMock, $featureSetMock);
                    break;
            }
        }

        $feature = new SequenceHelper();
        $feature->setTableGateway($tableGatewayMock);
        $feature->postInitialize();

        $this->assertEquals('foo_bar_seq', $tableGatewayMock->getSequence());
    }
}
