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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Helper\Navigation;

use Zend\View;

/**
 * Tests Zend_View_Helper_Navigation_Sitemap
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class SitemapTest extends AbstractTest
{
    protected $_oldServer = array();

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend\View\Helper\Navigation\Sitemap';

    /**
     * View helper
     *
     * @var Zend_View_Helper_Navigation_Sitemap
     */
    protected $_helper;

    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    protected function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
    	date_default_timezone_set('Europe/Berlin');

        if (isset($_SERVER['SERVER_NAME'])) {
            $this->_oldServer['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
        }

        if (isset($_SERVER['SERVER_PORT'])) {
            $this->_oldServer['SERVER_PORT'] = $_SERVER['SERVER_PORT'];
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->_oldServer['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        }

        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/';

        parent::setUp();

        $this->_helper->setFormatOutput(true);
        $this->_helper->getView()->plugin('basepath')->setBasePath('');
    }

    protected function tearDown()
    {
        foreach ($this->_oldServer as $key => $value) {
            $_SERVER[$key] = $value;
        }
        date_default_timezone_set($this->_originaltimezone);
    }

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->_helper->__invoke();
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->_helper->__invoke($this->_nav2);
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testNullingOutNavigation()
    {
        $this->_helper->setContainer();
        $this->assertEquals(0, count($this->_helper->getContainer()));
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $rendered1 = $this->_getExpected('sitemap/default1.xml');
        $rendered2 = $this->_getExpected('sitemap/default2.xml');

        $expected = array(
            'registered'       => $rendered1,
            'supplied'         => $rendered2,
            'registered_again' => $rendered1
        );
        $actual = array(
            'registered'       => $this->_helper->render(),
            'supplied'         => $this->_helper->render($this->_nav2),
            'registered_again' => $this->_helper->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUseAclRoles()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = $this->_getExpected('sitemap/acl.xml');
        $this->assertEquals($expected, $this->_helper->render());
    }

    public function testUseAclButNoRole()
    {
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole(null);

        $expected = $this->_getExpected('sitemap/acl2.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render());
        
        $this->assertEquals($expected, $got);
    }

    public function testSettingMaxDepth()
    {
        $this->_helper->setMaxDepth(0);

        $expected = $this->_getExpected('sitemap/depth1.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render());
        
        $this->assertEquals($expected, $got);
    }

    public function testSettingMinDepth()
    {
        $this->_helper->setMinDepth(1);

        $expected = $this->_getExpected('sitemap/depth2.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render());
        
        $this->assertEquals($expected, $got);
    }

    public function testSettingBothDepths()
    {
        $this->_helper->setMinDepth(1)->setMaxDepth(2);

        $expected = $this->_getExpected('sitemap/depth3.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render());
        
        $this->assertEquals($expected, $got);
    }

    public function testDropXmlDeclaration()
    {
        $this->_helper->setUseXmlDeclaration(false);

        $expected = $this->_getExpected('sitemap/nodecl.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render($this->_nav2));
        
        $this->assertEquals($expected, $got);
    }

    public function testThrowExceptionOnInvalidLoc()
    {
	    $this->markTestIncomplete('Zend\URI changes affect this test');
        $nav = clone $this->_nav2;
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->_helper->render($nav);
        } catch (View\Exception\ExceptionInterface $e) {
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
        $this->_helper->setUseSitemapValidators(false);

        $expected = $this->_getExpected('sitemap/invalid.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render($nav));
        
        $this->assertEquals($expected, $got);
    }

    public function testSetServerUrlRequiresValidUri()
    {
        $this->markTestIncomplete('Zend\URI changes affect this test');
        try {
            $this->_helper->setServerUrl('site.example.org');
            $this->fail('An invalid server URL was given, but a ' .
                        'Zend\URI\Exception\ExceptionInterface was not thrown');
        } catch (\Zend\URI\Exception\ExceptionInterface $e) {
            $this->assertContains('Illegal scheme', $e->getMessage());
        }
    }

    public function testSetServerUrlWithSchemeAndHost()
    {
        $this->_helper->setServerUrl('http://sub.example.org');

        $expected = $this->_getExpected('sitemap/serverurl1.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render());
        
        $this->assertEquals($expected, $got);
    }

    public function testSetServerUrlWithSchemeAndPortAndHostAndPath()
    {
        $this->_helper->setServerUrl('http://sub.example.org:8080/foo/');

        $expected = $this->_getExpected('sitemap/serverurl2.xml');
        $got = str_replace("\n", PHP_EOL, $this->_helper->render());
        
        $this->assertEquals($expected, $got);
    }

    public function testGetUserSchemaValidation()
    {
        $this->_helper->setUseSchemaValidation(true);
        $this->assertTrue($this->_helper->getUseSchemaValidation());
        $this->_helper->setUseSchemaValidation(false);
        $this->assertFalse($this->_helper->getUseSchemaValidation());
    }

    public function testUseSchemaValidation()
    {
        $this->markTestSkipped('Skipped because it fetches XSD from web');
        return;
        $nav = clone $this->_nav2;
        $this->_helper->setUseSitemapValidators(false);
        $this->_helper->setUseSchemaValidation(true);
        $nav->addPage(array('label' => 'Invalid', 'uri' => 'http://w.'));

        try {
            $this->_helper->render($nav);
        } catch (View\Exception\ExceptionInterface $e) {
            $expected = sprintf(
                    'Sitemap is invalid according to XML Schema at "%s"',
                    \Zend\View\Helper\Navigation\Sitemap::SITEMAP_XSD);
            $actual = $e->getMessage();
            $this->assertEquals($expected, $actual);
            return;
        }

        $this->fail('A Zend_View_Exception was not thrown when using Schema validation');
    }
}
