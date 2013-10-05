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

use Zend\Permissions\Rbac\Role;

/**
 * @category   Zend
 * @package    Zend_Permissions
 * @subpackage UnitTests
 * @group      Zend_Rbac
 */
class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testSetNameByConstructor()
    {
        $role = new Role('phpIsHell');
        $this->assertEquals('phpIsHell', $role->getName());
    }

    public function testCanSetParentRole()
    {
        $role   = new Role('children');
        $parent = new Role('parent');
        $role->setParent($parent);

        $this->assertSame($parent, $role->getParent());
    }

    public function testCanSetChildren()
    {
        $role  = new Role('php');
        $child = new Role('ror');

        $role->addChild($child);

        $this->assertSame($role, $child->getParent());
        $this->assertEquals(1, count($role->getChildren()));
    }

    public function testCanReadPermission()
    {
        $role = new Role('php');
        $role->addPermission('debug');

        $this->assertTrue($role->hasPermission('debug'));
    }

    public function testCanReadChildrenPermissions()
    {
        $role  = new Role('php');
        $child = new Role('ror');
        $role->addChild($child);

        $child->addPermission('debug');

        $this->assertTrue($role->hasPermission('debug'));
        $this->assertTrue($child->hasPermission('debug'));
    }

    public function testCannotReadParentPermission()
    {
        $role  = new Role('php');
        $child = new Role('ror');
        $role->addChild($child);

        $role->addPermission('debug');

        $this->assertTrue($role->hasPermission('debug'));
        $this->assertFalse($child->hasPermission('debug'));
    }
}
