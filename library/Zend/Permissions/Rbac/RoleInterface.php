<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions\Rbac;

use RecursiveIterator;

/**
 * Interface that all roles should implement
 *
 * The role embeds all the information needed to evaluate if a given role has a given permission
 */
interface RoleInterface extends RecursiveIterator
{
    /**
     * Get the name of the role.
     *
     * @return string
     */
    public function getName();

    /**
     * Add permission to the role.
     *
     * @param  PermissionInterface|string $permission
     * @return void
     */
    public function addPermission($permission);

    /**
     * Remove a permission from the role
     *
     * @param  PermissionInterface|string $permission
     * @return void
     */
    public function removePermission($permission);

    /**
     * Checks if a permission exists for this role (it does not check child roles)
     *
     * @param  PermissionInterface|string $permission
     * @return bool
     */
    public function hasPermission($permission);

    /**
     * Get all the permissions of the role (it does not get child role permissions)
     *
     * @return string[]|PermissionInterface[]
     */
    public function getPermissions();

    /**
     * Add a child
     *
     * @param  RoleInterface $child
     * @return void
     */
    public function addChild(RoleInterface $child);

    /**
     * Remove a child
     *
     * @param  RoleInterface $child
     * @return void
     */
    public function removeChild(RoleInterface $child);

    /**
     * Get children roles
     *
     * @return array|RoleInterface[]
     */
    public function getChildren();

    /**
     * Cast the role to a string (must return the role name)
     *
     * @return string
     */
    public function __toString();
}
