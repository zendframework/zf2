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

    public function testIsGrantedAssertion()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $true  = new TestAsset\SimpleTrueAssertion();
        $false = new TestAsset\SimpleFalseAssertion();

        $roleNoMatch = new TestAsset\RoleMustMatchAssertion($bar);
        $roleMatch   = new TestAsset\RoleMustMatchAssertion($foo);

        $foo->addPermission('can.foo');
        $bar->addPermission('can.bar');

        $this->rbac->addRole($foo);
        $this->rbac->addRole($bar);

        $this->assertEquals(true, $this->rbac->isGranted($foo, 'can.foo', $true));
        $this->assertEquals(false, $this->rbac->isGranted($bar, 'can.bar', $false));

        $this->assertEquals(false, $this->rbac->isGranted($bar, 'can.bar', $roleNoMatch));
        $this->assertEquals(false, $this->rbac->isGranted($bar, 'can.foo', $roleNoMatch));

        $this->assertEquals(true, $this->rbac->isGranted($foo, 'can.foo', $roleMatch));
    }

    public function testIsGrantedSingleRole()
    {
        $foo = new Rbac\Role('foo');
        $foo->addPermission('can.foo');

        $this->rbac->addRole($foo);

        $this->assertTrue($this->rbac->isGranted('foo', 'can.foo'));
        $this->assertFalse($this->rbac->isGranted('foo', 'can.bar'));
    }

    public function testIsGrantedChildRoles()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $foo->addPermission('can.foo');
        $bar->addPermission('can.bar');

        $this->rbac->addRole($bar);
        $this->rbac->addRole($foo, $bar);

        $this->assertTrue($this->rbac->isGranted('foo', 'can.foo'));
        $this->assertTrue($this->rbac->isGranted('foo', 'can.bar'));
        $this->assertTrue($this->rbac->isGranted('bar', 'can.bar'));

        $this->assertFalse($this->rbac->isGranted('foo', 'can.baz'));
        $this->assertFalse($this->rbac->isGranted('bar', 'can.baz'));
        $this->assertFalse($this->rbac->isGranted('bar', 'can.foo'));
    }

    public function testHasRole()
    {
        $foo = new Rbac\Role('foo');

        $this->rbac->addRole($foo);
        $this->rbac->addRole('bar');

        $this->assertTrue($this->rbac->hasRole($foo));
        $this->assertTrue($this->rbac->hasRole('bar'));
        $this->assertFalse($this->rbac->hasRole('baz'));
    }

    public function testAddRoleFromString()
    {
        $this->rbac->addRole('foo');

        $foo = $this->rbac->getRole('foo');

        $this->assertInstanceOf('Zend\Permissions\Rbac\Role', $foo);
    }

    public function testAddRoleFromClass()
    {
        $foo = new Rbac\Role('foo');

        $this->rbac->addRole($foo);

        $this->assertSame($foo, $this->rbac->getRole('foo'));
    }

    public function testAddRoleWithChildsUsingRbac()
    {
        $foo = new Rbac\Role('foo');
        $bar = new Rbac\Role('bar');

        $this->rbac->addRole($bar, $foo);

        $this->assertTrue($bar->hasChildren($foo));
        $this->assertSame($bar, $foo->getParent());
    }

    public function testAddCustomChildRole()
    {
        $role = $this->getMockForAbstractClass('Zend\Permissions\Rbac\RoleInterface');

        $this->rbac->addRole('parent', $role);

        $role->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('customchild'));

        $role->expects($this->once())
            ->method('hasPermission')
            ->with('test')
            ->will($this->returnValue(true));

        $this->assertTrue($this->rbac->isGranted('parent', 'test'));
    }
}
