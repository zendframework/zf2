<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper\Navigation;

use Zend\View\Helper\Navigation;

class AbstractHelperTest extends AbstractTest
{
    /**
     * View helper
     *
     * @var Navigation\AbstractHelper
     */
    protected $helper;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $helperName = 'Zend\View\Helper\Navigation';

    protected function tearDown()
    {
        parent::tearDown();
        $this->helper->setDefaultAcl(null);
        $this->helper->setAcl(null);
        $this->helper->setDefaultRole(null);
        $this->helper->setRole(null);
    }

    public function testHasACLChecksDefaultACL()
    {
        $aclContainer = $this->_getAcl();
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->helper->hasACL());
        $this->helper->setDefaultAcl($acl);
        $this->assertEquals(true, $this->helper->hasAcl());
    }

    public function testHasACLChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        $acl = $aclContainer['acl'];

        $this->assertEquals(false, $this->helper->hasAcl());
        $this->helper->setAcl($acl);
        $this->assertEquals(true, $this->helper->hasAcl());
    }

    public function testHasRoleChecksDefaultRole()
    {
        $aclContainer = $this->_getAcl();
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->helper->hasRole());
        $this->helper->setDefaultRole($role);
        $this->assertEquals(true, $this->helper->hasRole());
    }

    public function testHasRoleChecksMemberVariable()
    {
        $aclContainer = $this->_getAcl();
        $role = $aclContainer['role'];

        $this->assertEquals(false, $this->helper->hasRole());
        $this->helper->setRole($role);
        $this->assertEquals(true, $this->helper->hasRole());
    }
}
