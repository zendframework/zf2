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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config\Reader;

use \Zend\Config\Reader\Ini;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class IniTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        $this->reader = new Ini();
    }
    
    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     * 
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Ini/' . $name . '.ini';
    }
    
    public function testFromString()
    {
        $ini = <<<ECS
test= "foo"
bar[]= "baz"
bar[]= "foo"

ECS;
        
        $config = $this->reader->fromString($ini);
        $this->assertEquals($config['test'], 'foo');
        $this->assertEquals($config['bar'][0], 'baz');
        $this->assertEquals($config['bar'][1], 'foo');
    }
    
    public function testFromStringWithSection()
    {
        $ini = <<<ECS
[all]
test= "foo"
bar[]= "baz"
bar[]= "foo"

ECS;
        
        $config = $this->reader->fromString($ini);
        $this->assertEquals($config['all']['test'], 'foo');
        $this->assertEquals($config['all']['bar'][0], 'baz');
        $this->assertEquals($config['all']['bar'][1], 'foo');
    }
}
