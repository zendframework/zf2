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
 * @package    Zend_PDF
 * @subpackage UnitTests
 */

namespace ZendTest\Pdf\InternalType;
use Zend\Pdf\InternalType;

/**
 * \Zend\Pdf\InternalType\BooleanObject
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @group      Zend_PDF
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFBoolean()
    {
        $boolObj = new InternalType\BooleanObject(false);
        $this->assertTrue($boolObj instanceof InternalType\BooleanObject);
    }

    public function testPDFBooleanBadArgument()
    {
        $this->setExpectedException('\Zend\Pdf\Exception\RuntimeException', 'must be boolean');
        $boolObj = new InternalType\BooleanObject('some input');
    }

    public function testGetType()
    {
        $boolObj = new InternalType\BooleanObject((boolean) 100);
        $this->assertEquals($boolObj->getType(), InternalType\AbstractTypeObject::TYPE_BOOL);
    }

    public function testToString()
    {
        $boolObj = new InternalType\BooleanObject(true);
        $this->assertEquals($boolObj->toString(), 'true');
    }
}
