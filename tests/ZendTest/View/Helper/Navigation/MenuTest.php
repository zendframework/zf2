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

use Zend\View\Helper\Navigation\Menu;

class MenuTest extends AbstractTest
{
    /**
     * View helper
     *
     * @var Menu
     */
    protected $helper;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $helperName = 'Zend\View\Helper\Navigation\Menu';

    public function testCanRenderMenuFromServiceAlias()
    {
        $this->helper->setServiceLocator($this->serviceManager);

        $returned = $this->helper->renderMenu('Navigation');
        $this->assertEquals($returned, $this->_getExpected('menu/default1.html'));
    }

    public function testCanRenderPartialFromServiceAlias()
    {
        $this->helper->setPartial('menu.phtml');
        $this->helper->setServiceLocator($this->serviceManager);

        $returned = $this->helper->renderPartial('Navigation');
        $this->assertEquals($returned, $this->_getExpected('menu/partial.html'));
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

    public function testNullingOutContainerInHelper()
    {
        $this->helper->setContainer();
        $this->assertEquals(0, count($this->helper->getContainer()));
    }

    public function testSetIndentAndOverrideInRenderMenu()
    {
        $this->helper->setIndent(8);

        $expected = array(
            'indent4' => $this->_getExpected('menu/indent4.html'),
            'indent8' => $this->_getExpected('menu/indent8.html')
        );

        $renderOptions = array(
            'indent' => 4
        );

        $actual = array(
            'indent4' => rtrim($this->helper->renderMenu(null, $renderOptions), PHP_EOL),
            'indent8' => rtrim($this->helper->renderMenu(), PHP_EOL)
        );

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSuppliedContainerWithoutInterfering()
    {
        $rendered1 = $this->_getExpected('menu/default1.html');
        $rendered2 = $this->_getExpected('menu/default2.html');
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

    public function testUseAclRoleAsString()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole('member');

        $expected = $this->_getExpected('menu/acl_string.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testFilterOutPagesBasedOnAcl()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);

        $expected = $this->_getExpected('menu/acl.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAcl()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['role']);
        $this->helper->setUseAcl(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testUseAnAclRoleInstanceFromAclObject()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole($acl['acl']->getRole('member'));

        $expected = $this->_getExpected('menu/acl_role_interface.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testUseConstructedAclRolesNotFromAclObject()
    {
        $acl = $this->_getAcl();
        $this->helper->setAcl($acl['acl']);
        $this->helper->setRole(new \Zend\Permissions\Acl\Role\GenericRole('member'));

        $expected = $this->_getExpected('menu/acl_role_interface.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testSetUlCssClass()
    {
        $this->helper->setUlClass('My_Nav');
        $expected = $this->_getExpected('menu/css.html');
        $this->assertEquals($expected, $this->helper->render($this->_nav2));
    }

    public function testOptionEscapeLabelsAsTrue()
    {
        $options = array(
            'escapeLabels' => true
        );

        $container = new \Zend\Navigation\Navigation($this->_nav2->toArray());
        $container->addPage(array(
            'label' => 'Badges <span class="badge">1</span>',
            'uri' => 'badges'
        ));

        $expected = $this->_getExpected('menu/escapelabels_as_true.html');
        $actual = $this->helper->renderMenu($container, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionEscapeLabelsAsFalse()
    {
        $options = array(
            'escapeLabels' => false
        );

        $container = new \Zend\Navigation\Navigation($this->_nav2->toArray());
        $container->addPage(array(
            'label' => 'Badges <span class="badge">1</span>',
            'uri' => 'badges'
        ));

        $expected = $this->_getExpected('menu/escapelabels_as_false.html');
        $actual = $this->helper->renderMenu($container, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testTranslationUsingZendTranslate()
    {
        $translator = $this->_getTranslator();
        $this->helper->setTranslator($translator);

        $expected = $this->_getExpected('menu/translated.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testTranslationUsingZendTranslateAdapter()
    {
        $translator = $this->_getTranslator();
        $this->helper->setTranslator($translator);

        $expected = $this->_getExpected('menu/translated.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testDisablingTranslation()
    {
        $translator = $this->_getTranslator();
        $this->helper->setTranslator($translator);
        $this->helper->setTranslatorEnabled(false);

        $expected = $this->_getExpected('menu/default1.html');
        $this->assertEquals($expected, $this->helper->render());
    }

    public function testRenderingPartial()
    {
        $this->helper->setPartial('menu.phtml');

        $expected = $this->_getExpected('menu/partial.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialBySpecifyingAnArrayAsPartial()
    {
        $this->helper->setPartial(array('menu.phtml', 'application'));

        $expected = $this->_getExpected('menu/partial.html');
        $actual = $this->helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderingPartialShouldFailOnInvalidPartialArray()
    {
        $this->helper->setPartial(array('menu.phtml'));

        try {
            $this->helper->render();
            $this->fail('invalid $partial should throw Zend_View_Exception');
        } catch (\Zend\View\Exception\ExceptionInterface $e) {
        }
    }

    public function testSetMaxDepth()
    {
        $this->helper->setMaxDepth(1);

        $expected = $this->_getExpected('menu/maxdepth.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testSetMinDepth()
    {
        $this->helper->setMinDepth(1);

        $expected = $this->_getExpected('menu/mindepth.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testSetBothDepts()
    {
        $this->helper->setMinDepth(1)->setMaxDepth(2);

        $expected = $this->_getExpected('menu/bothdepts.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testSetOnlyActiveBranch()
    {
        $this->helper->setOnlyActiveBranch(true);

        $expected = $this->_getExpected('menu/onlyactivebranch.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testSetRenderParents()
    {
        $this->helper->setOnlyActiveBranch(true)->setRenderParents(false);

        $expected = $this->_getExpected('menu/onlyactivebranch_noparents.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testSetOnlyActiveBranchAndMinDepth()
    {
        $this->helper->setOnlyActiveBranch()->setMinDepth(1);

        $expected = $this->_getExpected('menu/onlyactivebranch_mindepth.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchAndMaxDepth()
    {
        $this->helper->setOnlyActiveBranch()->setMaxDepth(2);

        $expected = $this->_getExpected('menu/onlyactivebranch_maxdepth.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchAndBothDepthsSpecified()
    {
        $this->helper->setOnlyActiveBranch()->setMinDepth(1)->setMaxDepth(2);

        $expected = $this->_getExpected('menu/onlyactivebranch_bothdepts.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testOnlyActiveBranchNoParentsAndBothDepthsSpecified()
    {
        $this->helper->setOnlyActiveBranch()
                      ->setMinDepth(1)
                      ->setMaxDepth(2)
                      ->setRenderParents(false);

        $expected = $this->_getExpected('menu/onlyactivebranch_np_bd.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    private function _setActive($label)
    {
        $container = $this->helper->getContainer();

        foreach ($container->findAllByActive(true) as $page) {
            $page->setActive(false);
        }

        if ($p = $container->findOneByLabel($label)) {
            $p->setActive(true);
        }
    }

    public function testOnlyActiveBranchNoParentsActiveOneBelowMinDepth()
    {
        $this->_setActive('Page 2');

        $this->helper->setOnlyActiveBranch()
                      ->setMinDepth(1)
                      ->setMaxDepth(1)
                      ->setRenderParents(false);

        $expected = $this->_getExpected('menu/onlyactivebranch_np_bd2.html');
        $actual = $this->helper->renderMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testRenderSubMenuShouldOverrideOptions()
    {
        $this->helper->setOnlyActiveBranch(false)
                      ->setMinDepth(1)
                      ->setMaxDepth(2)
                      ->setRenderParents(true);

        $expected = $this->_getExpected('menu/onlyactivebranch_noparents.html');
        $actual = $this->helper->renderSubMenu();

        $this->assertEquals($expected, $actual);
    }

    public function testOptionMaxDepth()
    {
        $options = array(
            'maxDepth' => 1
        );

        $expected = $this->_getExpected('menu/maxdepth.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionMinDepth()
    {
        $options = array(
            'minDepth' => 1
        );

        $expected = $this->_getExpected('menu/mindepth.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionBothDepts()
    {
        $options = array(
            'minDepth' => 1,
            'maxDepth' => 2
        );

        $expected = $this->_getExpected('menu/bothdepts.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionOnlyActiveBranch()
    {
        $options = array(
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionOnlyActiveBranchNoParents()
    {
        $options = array(
            'onlyActiveBranch' => true,
            'renderParents' => false
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_noparents.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionOnlyActiveBranchAndMinDepth()
    {
        $options = array(
            'minDepth' => 1,
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_mindepth.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionOnlyActiveBranchAndMaxDepth()
    {
        $options = array(
            'maxDepth' => 2,
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_maxdepth.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionOnlyActiveBranchAndBothDepthsSpecified()
    {
        $options = array(
            'minDepth' => 1,
            'maxDepth' => 2,
            'onlyActiveBranch' => true
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_bothdepts.html');
        $actual = $this->helper->renderMenu(null, $options);

        $this->assertEquals($expected, $actual);
    }

    public function testOptionOnlyActiveBranchNoParentsAndBothDepthsSpecified()
    {
        $options = array(
            'minDepth' => 2,
            'maxDepth' => 2,
            'onlyActiveBranch' => true,
            'renderParents' => false
        );

        $expected = $this->_getExpected('menu/onlyactivebranch_np_bd.html');
        $actual = $this->helper->renderMenu(null, $options);

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
