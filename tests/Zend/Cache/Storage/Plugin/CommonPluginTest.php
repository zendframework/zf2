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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage\Plugin;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
abstract class CommonPluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage plugin
     *
     * @var Zend\Cache\Storage\Plugin
     */
    protected $_plugin;

    public function testOptionObjectAvailable()
    {
        $options = $this->_plugin->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Plugin\PluginOptions', $options);
    }

    public function testOptionsGetAndSetDefault()
    {
        $options = $this->_plugin->getOptions();
        $this->_plugin->setOptions($options);
        $this->assertSame($options, $this->_plugin->getOptions());
    }
}
