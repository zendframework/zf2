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
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config;

use \Zend\Config\Factory;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $_iniFileConfig;
    protected $_iniFileNested;

    public function testFromIni()
    {
        $config = Factory::fromFile(__DIR__ . '/TestAssets/Ini/include-base.ini');
        
        $this->assertEquals('bar', $config['base']['foo']);
    }
    
    public function testFromXml()
    {
        $config = Factory::fromFile(__DIR__ . '/TestAssets/Xml/include-base.xml');
        
        $this->assertEquals('bar', $config['base']['foo']);
    }
    
    public function testFromIniFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
            __DIR__ . '/TestAssets/Ini/include-base2.ini'
        );
        
        $config = Factory::fromFiles($files);
        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }
    
    public function testFromXmlFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Xml/include-base.xml',
            __DIR__ . '/TestAssets/Xml/include-base2.xml'
        );
        
        $config = Factory::fromFiles($files);
        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }
    
    public function testFromIniAndXmlFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
            __DIR__ . '/TestAssets/Xml/include-base2.xml'
        );
        
        $config = Factory::fromFiles($files);
        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testNonExistentFileThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $config = Factory::fromFile('foo.bar');
    }

    public function testInvalidFileExtensionThrowsInvalidArgumentException()
    {
        $this->setExpectedException('RuntimeException');
        $config = Factory::fromFile(__DIR__ . '/TestAssets/bad.ext');
    }
}

