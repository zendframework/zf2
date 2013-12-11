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

use RecursiveIteratorIterator;
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

    public function testCanCastToString()
    {
        $role = new Role('php');
        $this->assertEquals('php', (string) $role);
    }

    public function testCanAddChild()
    {
        $role  = new Role('php');
        $child = new Role('ror');
        $role->addChild($child);

        $count = 0;

        foreach ($role as $child) {
            $count++;
        }

        $this->assertEquals(1, $count);
    }

    public function testCanRemoveChild()
    {
        $role  = new Role('php');
        $child = new Role('ror');
        $role->addChild($child);
        $role->removeChild($child);

        $count = 0;

        foreach ($role as $child) {
            $count++;
        }

        $this->assertEquals(0, $count);
    }

    public function testRoleCanHavePermission()
    {
        $role = new Role('php');

        $role->addPermission('debug');
        $this->assertTrue($role->hasPermission('debug'));
        $this->assertCount(1, $role->getPermissions());

        $role->removePermission('debug');
        $this->assertFalse($role->hasPermission('debug'));
        $this->assertCount(0, $role->getPermissions());
    }

    public function testDontTestChildPermission()
    {
        $role  = new Role('php');
        $child = new Role('ror');

        $role->addChild($role);
        $child->addPermission('debug');

        $this->assertTrue($child->hasPermission('debug'));
        $this->assertFalse($role->hasPermission('debug'));
    }
}
