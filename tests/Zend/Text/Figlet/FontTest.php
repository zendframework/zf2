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
 * @package    Zend_Text_Figlet
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Text;

use Zend\Text\Figlet;
use Zend\Text\Figlet\Font;


/**
 * @category   Zend
 * @package    Zend_Text_Figlet
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Text
 */
class FontTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Letter F of default font
     *
     * @var array
     */
    protected $defaultF = array(
        0 => '  ______  ',
        1 => ' /_____// ',
        2 => ' `____ `  ',
        3 => ' /___//   ',
        4 => ' `__ `    ',
        5 => ' /_//     ',
        6 => ' `-`      ',
    );


    public function testDefaultFont()
    {
        $font = new Font();
        $this->assertEquals($this->defaultF, $font->getChar(102));
    }

    public function testLoadFont()
    {
        $font = new Font(__DIR__ . '/_files/DefaultFont.flf');
        $this->assertEquals($this->defaultF, $font->getChar(102));
    }

    public function testLoadFontFromString()
    {
        $string = file_get_contents(__DIR__ . '/_files/DefaultFont.flf');

        $font = new Font();
        $font->fromString($string);

        $this->assertEquals($this->defaultF, $font->getChar(102));
    }

    public function testGzippedFont()
    {
        $font = new Font(__DIR__ . '/_files/GzippedFont.gz');
        $this->assertEquals($this->defaultF, $font->getChar(102));
    }

    public function testNonExistentFont()
    {
        $this->setExpectedException('Zend\Text\Figlet\Exception\RuntimeException', 'not found');
        $font = new Font(__DIR__ . '/_files/NonExistentFont.flf');
    }

    public function testInvalidFont()
    {
        $this->setExpectedException('Zend\Text\Figlet\Exception\UnexpectedValueException', 'Not a FIGlet');
        $font = new Font(__DIR__ . '/_files/InvalidFont.flf');
    }
}
