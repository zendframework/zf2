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
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Text;

use Zend\Text\Figlet;
use Zend\Text\Figlet\FigletOptions;

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Text
 */
class FigletOptionsTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultOptions()
    {
        $options = new FigletOptions();
        $this->assertEquals($options->getOutputWidth(), 80);
        $this->assertEquals($options->getAlign(), FigletOptions::ALIGN_LEFT);
        $this->assertEquals($options->getDirection(), FigletOptions::DIRECTION_LTR);
        $this->assertEquals($options->getHandleParagraphs(), false);
    }

    public function testGetDefaultAlignment()
    {
        $options = new FigletOptions();
        $this->assertEquals($options->getAlign(), FigletOptions::ALIGN_LEFT);
    }

    public function testGetDefaultAlignmentRightToLeft()
    {
        $options = new FigletOptions();
        $options->setDirection(FigletOptions::DIRECTION_RTL);
        $this->assertEquals($options->getAlign(), FigletOptions::ALIGN_RIGHT);
    }

    public function testSetFontAsFileName()
    {
        $options = new FigletOptions();
        $options->setFont(__DIR__ . '/_files/DefaultFont.flf');
        $this->assertInstanceOf('Zend\\Text\\Figlet\\Font', $options->getFont());
    }

    public function testSetBadFont()
    {
        $this->setExpectedException('Zend\Text\Figlet\Exception\InvalidArgumentException',
                                    'instance of Zend\Text\Figlet\Font');
        $options = new FigletOptions(array('font' => 1));
    }
}
