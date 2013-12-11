<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Permissions
 */

namespace ZendTest\Permissions\Rbac;

use Zend\Permissions\Rbac;
use ZendTest\Permissions\Rbac\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Permissions
 * @subpackage UnitTests
 * @group      Zend_Rbac
 */
class RbacTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Permissions\Rbac\Rbac
     */
    protected $rbac;

    public function setUp()
    {
        $this->rbac = new Rbac\Rbac();
    }

    public function testCanAddRoleFromName()
    {
        $this->rbac->addRole('foo');
        $this->assertTrue($this->rbac->hasRole('foo'));
        $this->assertFalse($this->rbac->hasRole('baz'));
    }

    public function testCanAddRoleFromObject()
    {
        $role = new Rbac\Role('foo');
        $this->rbac->addRole($role);

        $this->assertTrue($this->rbac->hasRole($role));
        $this->assertTrue($this->rbac->hasRole('foo'));
        $this->assertFalse($this->rbac->hasRole('baz'));
    }

    public function testCannotAddRoleWithSameName()
    {
        $this->setExpectedException('Zend\Permissions\Rbac\Exception\InvalidArgumentException');

        $this->rbac->addRole('foo');
        $this->rbac->addRole('foo');
    }

    public function testCanGrantAccessWithHierarchyOfRoles()
    {
        $role       = new Rbac\Role('foo');
        $subRole    = new Rbac\Role('bar');
        $subSubRole = new Rbac\Role('baz');

        $role->addChild($subRole);
        $subRole->addChild($subSubRole);

        $subRole->addPermission('debug');

        $this->rbac->addRole($role);
        $this->rbac->addRole($subRole);
        $this->rbac->addRole($subSubRole);

        $this->assertTrue($this->rbac->isGranted($role, 'debug'), 'Inherit permission from its children');
        $this->assertTrue($this->rbac->isGranted($subRole, 'debug'), 'Have its own permission');
        $this->assertFalse($this->rbac->isGranted($subSubRole, 'debug'), 'Does not have permission from its parent');
    }

    public function testAssertions()
    {
        $role = new Rbac\Role('foo');
        $this->rbac->addRole($role);
        $role->addPermission('debug');

        $this->assertFalse($this->rbac->isGranted($role, 'debug', new TestAsset\SimpleFalseAssertion()));
        $this->assertTrue($this->rbac->isGranted($role, 'debug', new TestAsset\SimpleTrueAssertion()));
    }
}
