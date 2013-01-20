<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Log\LoggerComposite;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class LoggerCompositeTest extends TestCase
{
    public function testAddDebugAndRemoveLogger()
    {
        $logger1 = $this->getMockForAbstractClass('Zend\Log\LoggerInterface', array(), '', true, true, true, array(
           'debug'
        ));
        $logger1->expects($this->once())->method('debug')->with('message', array('extra1' => 'item1', 'extra2' => 'item2'));

        $logger2 = $this->getMockForAbstractClass('Zend\Log\LoggerInterface', array(), '', true, true, true, array(
            'debug'
        ));
        $logger2->expects($this->once())->method('debug')->with('message', array('extra1' => 'item1', 'extra2' => 'item2'));

        $composite = new LoggerComposite();
        $composite->addLogger($logger1);
        $composite->addLogger($logger2);
        $composite->debug('message', array('extra1' => 'item1', 'extra2' => 'item2'));
        $this->assertTrue($composite->hasLogger($logger1));
        $this->assertTrue($composite->hasLogger($logger2));
        $composite->removeLogger($logger1);
        $composite->removeLogger($logger2);
        $this->assertFalse($composite->hasLogger($logger1));
        $this->assertFalse($composite->hasLogger($logger2));
        $composite->debug('message', array('extra1' => 'item1', 'extra2' => 'item2'));
    }
}
