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
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\GApps;
use Zend\GData\GApps;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class ServiceExceptionTest extends \PHPUnit_Framework_TestCase
{
    protected $fixture;
    protected $data;

    public function setUp() {
        $this->xmlSample = file_get_contents(
                'Zend/GData/GApps/_files/AppsForYourDomainElementSample1.xml',
                true);
        $this->fixture = new GApps\ServiceException();
        $this->data[1] = new GApps\Error(1234, "foo", "bar");
        $this->data[2] = new GApps\Error(4317, "blah", "woof");
        $this->data[3] = new GApps\Error(5978, "blue", "puppy");
        $this->data[4] = new GApps\Error(2398, "red", "kitten");
    }

    public function testCanThrowServiceException()
    {
        $this->setExpectedException('Zend\GData\GApps\ServiceException');
        throw $this->fixture;
    }

    public function testCanSetAndGetErrorArray() {
        $this->fixture->setErrors($this->data);
        $incoming = $this->fixture->getErrors();
        $this->assertTrue(is_array($incoming));
        $this->assertEquals(count($this->data), count($incoming));
        foreach ($this->data as $i) {
            $this->assertEquals($i, $incoming[$i->getErrorCode()]);
        }
    }

    public function testCanInsertSingleError() {
        $this->fixture->setErrors($this->data);
        $outgoing = new GApps\Error(1111, "a", "b");
        $this->fixture->addError($outgoing);
        $result = $this->fixture->getError(1111);
        $this->assertEquals($outgoing, $result);
    }

    public function testCanSetPropertiesViaConstructor() {
        $this->fixture = new GApps\ServiceException($this->data);
        $incoming = $this->fixture->getErrors();
        $this->assertTrue(is_array($incoming));
        $this->assertEquals(count($this->data), count($incoming));
        foreach($this->data as $i) {
            $this->assertEquals($i, $incoming[$i->getErrorCode()]);
        }
    }

    public function testCanRetrieveASpecificErrorByCode() {
        $this->fixture->setErrors($this->data);
        $result = $this->fixture->getError(5978);
        $this->assertEquals($this->data[3], $result);
    }

    public function testRetrievingNonexistantErrorCodeReturnsNull() {
        $this->fixture->setErrors($this->data);
        $result = $this->fixture->getError(0000);
        $this->assertEquals(null, $result);
    }

    public function testCanCheckIfAKeyExists() {
        $this->fixture->setErrors($this->data);
        $this->assertTrue($this->fixture->hasError(2398));
        $this->assertFalse($this->fixture->hasError(0000));
    }

    public function testCanConvertFromXML() {
        $this->fixture->importFromString($this->xmlSample);
        $incoming = $this->fixture->getErrors();
        $this->assertTrue(is_array($incoming));
        $this->assertEquals(3, count($incoming));
        $this->assertEquals("9925", $incoming[9925]->errorCode);
        $this->assertEquals("Foo", $incoming[9925]->invalidInput);
        $this->assertEquals("Bar", $incoming[9925]->reason);
    }

    public function testCanConvertToString() {
        $this->fixture->setErrors($this->data);
        $this->assertEquals("The server encountered the following errors processing the request:
Error 1234: foo
\tInvalid Input: \"bar\"
Error 4317: blah
\tInvalid Input: \"woof\"
Error 5978: blue
\tInvalid Input: \"puppy\"
Error 2398: red
\tInvalid Input: \"kitten\"", $this->fixture->__toString());
    }

}
