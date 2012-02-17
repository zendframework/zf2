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

namespace ZendTest\Config\Reader;

use \Zend\Config\Reader\Xml;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class XmlTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        $this->reader = new Xml();
    }
    
    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     * 
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Xml/' . $name . '.xml';
    }
    
    public function testFromString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</zend-config>

ECS;
        
        $config = $this->reader->fromString($xml);
        $this->assertEquals($config['test'], 'foo');
        $this->assertEquals($config['bar'][0], 'baz');
        $this->assertEquals($config['bar'][1], 'foo');
    }
}