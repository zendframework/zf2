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

use Zend\Navigation\Navigation;
use Zend\View\Exception\ExceptionInterface;
use Zend\View\Helper\Navigation\Breadcrumbs;

class BreadcrumbsTest extends AbstractTest
{
    /**
     * View helper
     *
     * @var Breadcrumbs
     */
    protected $helper;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $helperName = 'Zend\View\Helper\Navigation\Breadcrumbs';

    public function testCanRenderStraightFromServiceAlias()
    {
        $this->helper->setServiceLocator($this->serviceManager);

        $returned = $this->helper->renderStraight('Navigation');
        $this->assertEquals($returned, $this->_getExpected('bc/default.html'));
    }

    public function testCanRenderPartialFromServiceAlias()
    {
        $this->helper->setPartial('bc.phtml');
        $this->helper->setServiceLocator($this->serviceManager);

        $returned = $this->helper->renderPartial('Navigation');
        $this->assertEquals($returned, $this->_getExpected('bc/partial.html'));
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

    public function testHelperEntryPointWithContainerStringParam()
    {
        $pm = new \Zend\View\HelperPluginManager;
        $pm->setServiceLocator($this->serviceManager);
        $this->helper->setServiceLocator($pm);

        $returned = $this->helper->__invoke('nav1');
        $this->assertEquals($this->helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testNullOutContainer()
    {
        $old = $this->helper->getContainer();
        $this->helper->setContainer();
        $new = $this->helper->getContainer();

        $this->assertNotEquals($old, $new);
    }

    public function testSetSeparator()
    {
        $this->helper->setSeparator('foo');

        $expected = $this->_getExpected('bc/separator.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testSetMaxDepth()
    {
        $this->helper->setMaxDepth(1);

        $expected = $this->_getExpected('bc/maxdepth.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testSetMinDepth()
    {
        $this->helper->setMinDepth(1);

        $expected = '';
        $this->assertEquals($expected, $this->helper->render($this->_nav2));
    }

    public function testLinkLastElement()
    {
        $this->helper->setLinkLast(true);

        $expected = $this->_getExpected('bc/linklast.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testSetIndent()
    {
        $this->helper->setIndent(8);

        $expected = '        <a';
        $actual = substr($this->helper->render(), 0, strlen($expected));

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $this->helper->setMinDepth(0);

        $rendered1 = $this->_getExpected('bc/default.html');
        $rendered2 = 'Site 2';

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

    public function testUseAclResourceFromPages()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);

        $expected = $this->_getExpected('bc/acl.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testTranslationUsingZendTranslate()
    {
        $this->helper->setTranslator($this->_getTranslator());

        $expected = $this->_getExpected('bc/translated.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->helper->setTranslator($translator);

        $expected = $this->_getExpected('bc/translated.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->helper->setTranslator($translator);
        $this->helper->setTranslatorEnabled(false);

        $expected = $this->_getExpected('bc/default.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testRenderingPartial()
    {
        $this->helper->setPartial('bc.phtml');

        $expected = $this->_getExpected('bc/partial.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testRenderingPartialBySpecifyingAnArrayAsPartial()
    {
        $this->helper->setPartial(array('bc.phtml', 'application'));

        $expected = $this->_getExpected('bc/partial.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testRenderingPartialShouldFailOnInvalidPartialArray()
    {
        $this->helper->setPartial(array('bc.phtml'));

        try {
            $this->helper->render();
            $this->fail(
                '$partial was invalid, but no Zend\View\Exception\ExceptionInterface was thrown');
        } catch (ExceptionInterface $e) {
        }
    }

    public function testLastBreadcrumbShouldBeEscaped()
    {
        $container = new Navigation(array(
            array(
                'label'  => 'Live & Learn',
                'uri'    => '#',
                'active' => true
            )
        ));

        $expected = 'Live &amp; Learn';
        $actual = $this->helper->setMinDepth(0)->render($container);

        $this->assertEquals($expected, $actual);
    }
}
