<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper\HeadMeta\Plugin;

use Zend\View\Helper\HeadMeta\Plugin\OpenGraph as OpenGraphPlugin;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 * @group      Zend_View_Helper_HeadMeta
 */
class OpenGraphTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OpenGraphPlugin
     */
    public $plugin;

    public function setUp()
    {
        $this->helper = new OpenGraphPlugin();
    }

    public function provideOgCalls()
    {
        return array(
            array('setOgTitle', 'og:title', 'Test', array()),
            array('setOgDescription', 'og:description', 'test test test', array('foo', 'bar')),
            array('setOg_ArticleAuthor', 'article:author', 'http://www.example.com/author/1', array()), //Custom type notation
        );
    }

    /**
     * @dataProvider provideOgCalls
     */
    public function testOgAwareCallHandled($method, $expectedProperty, $content, $modifiers = array())
    {
        $result = $this->helper->handle($method, array($content, $modifiers));

        $this->assertEquals('set', $result['action']);
        $this->assertEquals(OpenGraphPlugin::META_TYPE, $result['type']);
        $this->assertEquals($expectedProperty, $result['typeValue']);
        $this->assertEquals($content, $result['content']);
        $this->assertEquals($modifiers, $result['modifiers']);
    }

    public function testUnknownMethodNotHandled()
    {
        $result = $this->helper->handle('setFooBar', array('Test'));

        $this->assertFalse($result);
    }

    public function testSupportedPropertiesValidation()
    {
        try {
            $this->helper->handle('setOgFoobar', array('Test'));
            $this->fail('Expected \Zend\View\Exception\BadMethodCallException not thrown');
        } catch (\Zend\View\Exception\BadMethodCallException $ex) {
            $this->assertContains('unsupported open graph property', strtolower($ex->getMessage()));
        }
    }

    public function testSupportedCustomTypesValidation()
    {
        try {
            $this->helper->handle('setOg_FooTest', array('Test'));
            $this->fail('Expected \Zend\View\Exception\BadMethodCallException not thrown');
        } catch (\Zend\View\Exception\BadMethodCallException $ex) {
            $this->assertContains('unsupported custom open graph type', strtolower($ex->getMessage()));
        }
    }
}
