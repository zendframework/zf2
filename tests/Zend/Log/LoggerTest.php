<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log;

use Zend\Log\Logger,
    Zend\Log\Writer\Mock as MockWriter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->logger = new Logger;
    }

    public function testUsesDateFormatIso8601ByDefault()
    {
        $this->assertEquals('c', $this->logger->getDateTimeFormat());
    }

    public function testPassingStringToSetDateTimeFormat()
    {
        $this->logger->setDateTimeFormat('U');
        $this->assertEquals('U', $this->logger->getDateTimeFormat());
    }

    public function testUsesWriterBrokerByDefault()
    {
        $this->assertInstanceOf('Zend\Log\WriterBroker', $this->logger->getBroker());
    }

    public function testPassingValidStringClassToSetBroker()
    {
        $this->logger->setBroker('Zend\Loader\PluginBroker');
        $this->assertInstanceOf('Zend\Loader\PluginBroker', $this->logger->getBroker());
    }

    public static function provideInvalidArguments()
    {
        return array(
          array('stdClass'),
          array(new \stdClass()),
        );
    }

    /**
     * @dataProvider provideInvalidArguments
     */
    public function testPassingInvalidArgumentToSetBrokerRaisesException($broker)
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'must implement');
        $this->logger->setBroker($broker);
    }

    public function testPassingShortNameToBrokerReturnsWriterByThatName()
    {
        $writer = $this->logger->plugin('mock');
        $this->assertInstanceOf('Zend\Log\Writer\Mock', $writer);
    }

    /**
     * @dataProvider provideInvalidArguments
     */
    public function testPassingInvalidArgumentToAddWriterRaisesException($writer)
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'must implement');
        $this->logger->addWriter($writer);
    }

    public function testWriterShouldEmitMessage()
    {
        $writer = new MockWriter;
        $this->logger->addWriter($writer);
        $this->logger->log(Logger::INFO, 'tottakai');

        $this->assertArrayHasKey(0, $writer->events);
        $this->assertContains('tottakai', $writer->events[0]);
    }
}
