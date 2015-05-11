<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Feed\Reader;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Feed\Reader\StandaloneExtensionManager;

class StandaloneExtensionManagerTest extends TestCase
{
    public function setUp()
    {
        $this->extensions = new StandaloneExtensionManager();
    }

    public function testIsAnExtensionManagerImplementation()
    {
        $this->assertInstanceOf('Zend\Feed\Reader\ExtensionManagerInterface', $this->extensions);
    }

    public function defaultPlugins()
    {
        return [
            'Atom\Entry'            => ['Atom\Entry', 'Zend\Feed\Reader\Extension\Atom\Entry'],
            'Atom\Feed'             => ['Atom\Feed', 'Zend\Feed\Reader\Extension\Atom\Feed'],
            'Content\Entry'         => ['Content\Entry', 'Zend\Feed\Reader\Extension\Content\Entry'],
            'CreativeCommons\Entry' => [
                'CreativeCommons\Entry',
                'Zend\Feed\Reader\Extension\CreativeCommons\Entry'
            ],
            'CreativeCommons\Feed'  => ['CreativeCommons\Feed', 'Zend\Feed\Reader\Extension\CreativeCommons\Feed'],
            'DublinCore\Entry'      => ['DublinCore\Entry', 'Zend\Feed\Reader\Extension\DublinCore\Entry'],
            'DublinCore\Feed'       => ['DublinCore\Feed', 'Zend\Feed\Reader\Extension\DublinCore\Feed'],
            'Podcast\Entry'         => ['Podcast\Entry', 'Zend\Feed\Reader\Extension\Podcast\Entry'],
            'Podcast\Feed'          => ['Podcast\Feed', 'Zend\Feed\Reader\Extension\Podcast\Feed'],
            'Slash\Entry'           => ['Slash\Entry', 'Zend\Feed\Reader\Extension\Slash\Entry'],
            'Syndication\Feed'      => ['Syndication\Feed', 'Zend\Feed\Reader\Extension\Syndication\Feed'],
            'Thread\Entry'          => ['Thread\Entry', 'Zend\Feed\Reader\Extension\Thread\Entry'],
            'WellFormedWeb\Entry'   => ['WellFormedWeb\Entry', 'Zend\Feed\Reader\Extension\WellFormedWeb\Entry'],
        ];
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testHasAllDefaultPlugins($pluginName, $pluginClass)
    {
        $this->assertTrue($this->extensions->has($pluginName));
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testCanRetrieveDefaultPluginInstances($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testEachPluginRetrievalReturnsNewInstance($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);

        $test = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $test);
        $this->assertNotSame($extension, $test);
    }
}
