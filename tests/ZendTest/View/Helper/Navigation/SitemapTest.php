<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper\Navigation;

use DOMDocument;
use Zend\View\Exception;
use Zend\View\Helper\Navigation\Sitemap;

class SitemapTest extends AbstractTest
{
    /**
     * View helper
     *
     * @var Sitemap
     */
    protected $helper;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $helperName = 'Zend\View\Helper\Navigation\Sitemap';

    /**
     * Stores the original set timezone
     * @var string
     */
    protected $originalTimezone;

    /**
     * @var array
     */
    protected $oldServer = array();

    protected function setUp()
    {
        $this->originalTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');

        if (isset($_SERVER['SERVER_NAME'])) {
            $this->oldServer['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
        }

        if (isset($_SERVER['SERVER_PORT'])) {
            $this->oldServer['SERVER_PORT'] = $_SERVER['SERVER_PORT'];
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->oldServer['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        }

        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/';

        parent::setUp();

        $this->helper->setFormatOutput(true);
        $this->helper->getView()->plugin('basepath')->setBasePath('');
    }

    protected function tearDown()
    {
        foreach ($this->oldServer as $key => $value) {
            $_SERVER[$key] = $value;
        }
        date_default_timezone_set($this->originalTimezone);
    }

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->helper->__invoke();
        $this->assertEquals($this->helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->helper->__invoke($this->_nav2);
        $this->assertEquals($this->helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testNullingOutNavigation()
    {
        $this->helper->setContainer();
        $this->assertEquals(0, count($this->helper->getContainer()));
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $rendered1 = trim($this->_getExpected('sitemap/default1.xml'));
        $rendered2 = trim($this->_getExpected('sitemap/default2.xml'));

        $expected = array(
            'registered'       => $rendered1,
            'supplied'         => $rendered2,
            'registered_again' => $rendered1
        );
        $actual = array(
            'registered'       => $this->helper->render(),
            'supplied'         => $this->helper->render($this->_nav2),
            'registered_again' => $this->helper->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUseAclRoles()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);

        $expected = $this->_getExpected('sitemap/acl.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testUseAclButNoRole()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole(null);

        $expected = $this->_getExpected('sitemap/acl2.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testSettingMaxDepth()
    {
        $this->helper->setMaxDepth(0);

        $expected = $this->_getExpected('sitemap/depth1.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testSettingMinDepth()
    {
        $this->helper->setMinDepth(1);

        $expected = $this->_getExpected('sitemap/depth2.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testSettingBothDepths()
    {
        $this->helper->setMinDepth(1)->setMaxDepth(2);

        $expected = $this->_getExpected('sitemap/depth3.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testDropXmlDeclaration()
    {
        $this->helper->setUseXmlDeclaration(false);

        $expected = $this->_getExpected('sitemap/nodecl.xml');
        $this->assertEquals(trim($expected), $this->helper->render($this->_nav2));
    }

    public function testThrowExceptionOnInvalidLoc()
    {
        $this->markTestIncomplete('Zend\URI changes affect this test');
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->helper->render($nav);
        } catch (Exception\ExceptionInterface $e) {
            $expected = sprintf(
                    'Encountered an invalid URL for Sitemap XML: "%s"',
                    'http://w.');
            $actual = $e->getMessage();
            $this->assertEquals($expected, $actual);
            return;
        }

        $this->fail('A Zend_View_Exception was not thrown on invalid <loc />');
    }

    public function testDisablingValidators()
    {
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));
        $this->helper->setUseSitemapValidators(false);

        $expected = $this->_getExpected('sitemap/invalid.xml');

        // using assertEqualXMLStructure to prevent differences in libxml from invalidating test
        $expectedDom = new DOMDocument();
        $receivedDom = new DOMDocument();
        $expectedDom->loadXML($expected);
        $receivedDom->loadXML($this->helper->render($nav));
        $this->assertEqualXMLStructure($expectedDom->documentElement, $receivedDom->documentElement);
    }

    public function testSetServerUrlRequiresValidUri()
    {
        $this->markTestIncomplete('Zend\URI changes affect this test');
        try {
            $this->helper->setServerUrl('site.example.org');
            $this->fail('An invalid server URL was given, but a ' .
                        'Zend\URI\Exception\ExceptionInterface was not thrown');
        } catch (\Zend\URI\Exception\ExceptionInterface $e) {
            $this->assertContains('Illegal scheme', $e->getMessage());
        }
    }

    public function testSetServerUrlWithSchemeAndHost()
    {
        $this->helper->setServerUrl('http://sub.example.org');

        $expected = $this->_getExpected('sitemap/serverurl1.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testSetServerUrlWithSchemeAndPortAndHostAndPath()
    {
        $this->helper->setServerUrl('http://sub.example.org:8080/foo/');

        $expected = $this->_getExpected('sitemap/serverurl2.xml');
        $this->assertEquals(trim($expected), $this->helper->render());
    }

    public function testGetUserSchemaValidation()
    {
        $this->helper->setUseSchemaValidation(true);
        $this->assertTrue($this->helper->getUseSchemaValidation());
        $this->helper->setUseSchemaValidation(false);
        $this->assertFalse($this->helper->getUseSchemaValidation());
    }

    public function testUseSchemaValidation()
    {
        $this->markTestSkipped('Skipped because it fetches XSD from web');
        return;
        $nav = clone $this->_nav2;
        $this->helper->setUseSitemapValidators(false);
        $this->helper->setUseSchemaValidation(true);
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->helper->render($nav);
        } catch (Exception\ExceptionInterface $e) {
            $expected = sprintf(
                    'Sitemap is invalid according to XML Schema at "%s"',
                    Sitemap::SITEMAP_XSD);
            $actual = $e->getMessage();
            $this->assertEquals($expected, $actual);
            return;
        }

        $this->fail('A Zend_View_Exception was not thrown when using Schema validation');
    }
}
