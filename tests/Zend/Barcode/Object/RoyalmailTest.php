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
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Barcode\Object;
use Zend\Barcode;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RoyalmailTest extends TestCommon
{
    protected function getBarcodeObject($options = null)
    {
        return new Barcode\Object\Royalmail($options);
    }

    public function testType()
    {
        $this->assertSame('royalmail', $this->object->getType());
    }

    public function testChecksum()
    {
        $this->assertSame('W', $this->object->getChecksum('012345'));
        $this->assertSame('H', $this->object->getChecksum('06CIOU'));
        $this->assertSame('K', $this->object->getChecksum('SN34RD1A'));
    }

    public function testSetText()
    {
        $this->object->setText('012345');
        $this->assertSame('012345', $this->object->getRawText());
        $this->assertSame('012345W', $this->object->getText());
        $this->assertSame('012345W', $this->object->getTextToDisplay());
    }

    public function testSetTextWithoutChecksumHasNoEffect()
    {
        $this->object->setText('012345');
        $this->object->setWithChecksum(false);
        $this->assertSame('012345', $this->object->getRawText());
        $this->assertSame('012345W', $this->object->getText());
        $this->assertSame('012345W', $this->object->getTextToDisplay());
    }

    public function testSetTextWithSpaces()
    {
        $this->object->setText(' 012345 ');
        $this->assertSame('012345', $this->object->getRawText());
        $this->assertSame('012345W', $this->object->getText());
        $this->assertSame('012345W', $this->object->getTextToDisplay());
    }

    public function testSetTextWithChecksumNotDisplayed()
    {
        $this->object->setText('012345');
        $this->object->setWithChecksumInText(false);
        $this->assertSame('012345', $this->object->getRawText());
        $this->assertSame('012345W', $this->object->getText());
        $this->assertSame('012345W', $this->object->getTextToDisplay());
    }

    public function testBadTextDetectedIfChecksumWished()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception');
        $this->object->setText('a');
        $this->object->setWithChecksum(true);
        $this->object->getText();
    }

    public function testCheckGoodParams()
    {
        $this->object->setText('012345');
        $this->assertTrue($this->object->checkParams());
    }

    public function testGetKnownWidthWithoutOrientation()
    {
        $this->object->setText('012345');
        $this->assertEquals(158, $this->object->getWidth());
        $this->object->setWithQuietZones(false);
        $this->assertEquals(118, $this->object->getWidth(true));
    }

    public function testCompleteGeneration()
    {
        $this->object->setText('012345');
        $this->object->draw();
        $instructions = $this->loadInstructionsFile('Royalmail_012345_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorder()
    {
        $this->object->setText('012345');
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Royalmail_012345_border_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithOrientation()
    {
        $this->object->setText('012345');
        $this->object->setOrientation(60);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Royalmail_012345_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testCompleteGenerationWithBorderWithOrientation()
    {
        $this->object->setText('012345');
        $this->object->setOrientation(60);
        $this->object->setWithBorder(true);
        $this->object->draw();
        $instructions = $this->loadInstructionsFile(
                'Royalmail_012345_border_oriented_instructions');
        $this->assertEquals($instructions, $this->object->getInstructions());
    }

    public function testGetDefaultHeight()
    {
        // Checksum activated => text needed
        $this->object->setText('012345');
        $this->assertEquals(20, $this->object->getHeight(true));
    }
}
