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

use Zend\Navigation\Navigation as Container;
use Zend\Permissions\Acl;
use Zend\Permissions\Acl\Role;
use Zend\ServiceManager\ServiceManager;
use Zend\View;
use Zend\View\Helper\Navigation;

class NavigationTest extends AbstractTest
{
    /**
     * View helper
     *
     * @var Navigation
     */
    protected $helper;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $helperName = 'Zend\View\Helper\Navigation';

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

    public function testAcceptAclShouldReturnGracefullyWithUnknownResource()
    {
        // setup
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);

        $accepted = $this->helper->accept(
            new \Zend\Navigation\Page\Uri(array(
                'resource'  => 'unknownresource',
                'privilege' => 'someprivilege'
            ),
            false)
        );

        $this->assertEquals($accepted, false);
    }

    public function testShouldProxyToMenuHelperByDefault()
    {
        $this->helper->setContainer($this->_nav1);

        // result
        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testHasContainer()
    {
        $oldContainer = $this->helper->getContainer();
        $this->helper->setContainer(null);
        $this->assertFalse($this->helper->hasContainer());
        $this->helper->setContainer($oldContainer);
    }

    public function testInjectingContainer()
    {
        // setup
        $this->helper->setContainer($this->_nav2);
        $expected = array(
            'menu' => $this->_getExpected('menu/default2.html'),
            'breadcrumbs' => $this->_getExpected('bc/default.html')
        );
        $actual = array();

        // result
        $actual['menu'] = $this->helper->render();
        $this->helper->setContainer($this->_nav1);
        $actual['breadcrumbs'] = $this->helper->breadcrumbs()->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingContainerInjection()
    {
        // setup
        $this->helper->setInjectContainer(false);
        $this->helper->menu()->setContainer(null);
        $this->helper->breadcrumbs()->setContainer(null);
        $this->helper->setContainer($this->_nav2);

        // result
        $expected = array(
            'menu'        => '',
            'breadcrumbs' => ''
        );
        $actual = array(
            'menu'        => $this->helper->render(),
            'breadcrumbs' => $this->helper->breadcrumbs()->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testServiceManagerIsUsedToRetrieveContainer()
    {
        $container      = new Container;
        $serviceManager = new ServiceManager;
        $serviceManager->setService('navigation', $container);

        $pluginManager  = new View\HelperPluginManager;
        $pluginManager->setServiceLocator($serviceManager);

        $this->helper->setServiceLocator($pluginManager);
        $this->helper->setContainer('navigation');

        $expected = $this->helper->getContainer();
        $actual   = $container;
        $this->assertEquals($expected, $actual);
    }

    public function testInjectingAcl()
    {
        // setup
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);

        $expected = $this->_getExpected('menu/acl.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAclInjection()
    {
        // setup
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);
        $this->helper->setInjectAcl(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testInjectingTranslator()
    {
        $this->helper->setTranslator($this->_getTranslator());

        $expected = $this->_getExpected('menu/translated.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingTranslatorInjection()
    {
        $this->helper->setTranslator($this->_getTranslator());
        $this->helper->setInjectTranslator(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTranslatorMethods()
    {
        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator');
        $this->helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->helper->getTranslator());
        $this->assertEquals('foo', $this->helper->getTranslatorTextDomain());
        $this->assertTrue($this->helper->hasTranslator());
        $this->assertTrue($this->helper->isTranslatorEnabled());

        $this->helper->setTranslatorEnabled(false);
        $this->assertFalse($this->helper->isTranslatorEnabled());
    }

    public function testSpecifyingDefaultProxy()
    {
        $expected = array(
            'breadcrumbs' => $this->_getExpected('bc/default.html'),
            'menu' => $this->_getExpected('menu/default1.html')
        );
        $actual = array();

        // result
        $this->helper->setDefaultProxy('breadcrumbs');
        $actual['breadcrumbs'] = $this->helper->render($this->_nav1);
        $this->helper->setDefaultProxy('menu');
        $actual['menu'] = $this->helper->render($this->_nav1);

        $this->assertEquals($expected, $actual);
    }

    public function testGetAclReturnsNullIfNoAclInstance()
    {
        $this->assertNull($this->helper->getAcl());
    }

    public function testGetAclReturnsAclInstanceSetWithSetAcl()
    {
        $acl = new Acl\Acl();
        $this->helper->setAcl($acl);
        $this->assertEquals($acl, $this->helper->getAcl());
    }

    public function testGetAclReturnsAclInstanceSetWithSetDefaultAcl()
    {
        $acl = new Acl\Acl();
        Navigation\AbstractHelper::setDefaultAcl($acl);
        $actual = $this->helper->getAcl();
        Navigation\AbstractHelper::setDefaultAcl(null);
        $this->assertEquals($acl, $actual);
    }

    public function testSetDefaultAclAcceptsNull()
    {
        $acl = new Acl\Acl();
        Navigation\AbstractHelper::setDefaultAcl($acl);
        Navigation\AbstractHelper::setDefaultAcl(null);
        $this->assertNull($this->helper->getAcl());
    }

    public function testSetDefaultAclAcceptsNoParam()
    {
        $acl = new Acl\Acl();
        Navigation\AbstractHelper::setDefaultAcl($acl);
        Navigation\AbstractHelper::setDefaultAcl();
        $this->assertNull($this->helper->getAcl());
    }

    public function testSetRoleAcceptsString()
    {
        $this->helper->setRole('member');
        $this->assertEquals('member', $this->helper->getRole());
    }

    public function testSetRoleAcceptsRoleInterface()
    {
        $role = new Role\GenericRole('member');
        $this->helper->setRole($role);
        $this->assertEquals($role, $this->helper->getRole());
    }

    public function testSetRoleAcceptsNull()
    {
        $this->helper->setRole('member')->setRole(null);
        $this->assertNull($this->helper->getRole());
    }

    public function testSetRoleAcceptsNoParam()
    {
        $this->helper->setRole('member')->setRole();
        $this->assertNull($this->helper->getRole());
    }

    public function testSetRoleThrowsExceptionWhenGivenAnInt()
    {
        try {
            $this->helper->setRole(1337);
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be a string', $e->getMessage());
        }
    }

    public function testSetRoleThrowsExceptionWhenGivenAnArbitraryObject()
    {
        try {
            $this->helper->setRole(new \stdClass());
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be a string', $e->getMessage());
        }
    }

    public function testSetDefaultRoleAcceptsString()
    {
        $expected = 'member';
        Navigation\AbstractHelper::setDefaultRole($expected);
        $actual = $this->helper->getRole();
        Navigation\AbstractHelper::setDefaultRole(null);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultRoleAcceptsRoleInterface()
    {
        $expected = new Role\GenericRole('member');
        Navigation\AbstractHelper::setDefaultRole($expected);
        $actual = $this->helper->getRole();
        Navigation\AbstractHelper::setDefaultRole(null);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultRoleAcceptsNull()
    {
        Navigation\AbstractHelper::setDefaultRole(null);
        $this->assertNull($this->helper->getRole());
    }

    public function testSetDefaultRoleAcceptsNoParam()
    {
        Navigation\AbstractHelper::setDefaultRole();
        $this->assertNull($this->helper->getRole());
    }

    public function testSetDefaultRoleThrowsExceptionWhenGivenAnInt()
    {
        try {
            Navigation\AbstractHelper::setDefaultRole(1337);
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be', $e->getMessage());
        }
    }

    public function testSetDefaultRoleThrowsExceptionWhenGivenAnArbitraryObject()
    {
        try {
            Navigation\AbstractHelper::setDefaultRole(new \stdClass());
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be', $e->getMessage());
        }
    }

    private $_errorMessage;
    public function toStringErrorHandler($code, $msg, $file, $line, array $c)
    {
        $this->_errorMessage = $msg;
    }

    public function testMagicToStringShouldNotThrowException()
    {
        set_error_handler(array($this, 'toStringErrorHandler'));
        $this->helper->menu()->setPartial(array(1337));
        $this->helper->__toString();
        restore_error_handler();

        $this->assertContains('array must contain two values', $this->_errorMessage);
    }

    public function testPageIdShouldBeNormalized()
    {
        $nl = PHP_EOL;

        $container = new \Zend\Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'id'    => 'p1',
                'uri'   => 'p1'
            ),
            array(
                'label' => 'Page 2',
                'id'    => 'p2',
                'uri'   => 'p2'
            )
        ));

        $expected = '<ul class="navigation">' . $nl
                  . '    <li>' . $nl
                  . '        <a id="menu-p1" href="p1">Page 1</a>' . $nl
                  . '    </li>' . $nl
                  . '    <li>' . $nl
                  . '        <a id="menu-p2" href="p2">Page 2</a>' . $nl
                  . '    </li>' . $nl
                  . '</ul>';

        $actual = $this->helper->render($container);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-6854
     */
    public function testRenderInvisibleItem()
    {
        $container = new \Zend\Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'id'    => 'p1',
                'uri'   => 'p1'
            ),
            array(
                'label'   => 'Page 2',
                'id'      => 'p2',
                'uri'     => 'p2',
                'visible' => false
            )
        ));

        $render = $this->helper->menu()->render($container);

        $this->assertFalse(strpos($render, 'p2'));

        $this->helper->menu()->setRenderInvisible();

        $render = $this->helper->menu()->render($container);

        $this->assertTrue(strpos($render, 'p2') !== false);
    }

    public function testMultipleNavigations()
    {
        $sm   = new ServiceManager();
        $nav1 = new Container();
        $nav2 = new Container();
        $sm->setService('nav1', $nav1);
        $sm->setService('nav2', $nav2);

        $helper = new Navigation();
        $helper->setServiceLocator($sm);

        $menu     = $helper('nav1')->menu();
        $actual   = spl_object_hash($nav1);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);

        $menu     = $helper('nav2')->menu();
        $actual   = spl_object_hash($nav2);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);
    }

    /**
     * Returns the contens of the expected $file, normalizes newlines
     * @param  string $file
     * @return string
     */
    protected function _getExpected($file)
    {
        return str_replace("\n", PHP_EOL, parent::_getExpected($file));
    }
}
