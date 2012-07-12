<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\StrikeIron;

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class ZipCodeInfoTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->soapClient = new \stdclass();
        $this->service = new \Zend\Service\StrikeIron\ZipCodeInfo(array('client' => $this->soapClient));
    }

    public function testInheritsFromBase()
    {
        $this->assertInstanceOf('Zend\Service\StrikeIron\Base', $this->service);
    }

    public function testHasCorrectWsdl()
    {
        $wsdl = 'http://sdpws.strikeiron.com/zf1.StrikeIron/sdpZIPCodeInfo?WSDL';
        $this->assertEquals($wsdl, $this->service->getWsdl());
    }

    public function testInstantiationFromFactory()
    {
        $strikeIron = new \Zend\Service\StrikeIron\StrikeIron(array('client' => $this->soapClient));
        $client = $strikeIron->getService(array('class' => 'ZipCodeInfo'));

        $this->assertInstanceOf('Zend\Service\StrikeIron\ZipCodeInfo', $client);
    }

}
