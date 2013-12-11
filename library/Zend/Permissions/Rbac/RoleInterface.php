<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions\Rbac;

use IteratorAggregate;

/**
 * Interface that all roles should implement
 *
 * The role embeds all the information needed to evaluate if a given role has a given permission. Here is
 * a recap of some properties of a role:
 *
 *      - A role MUST have a name
 *      - A role MAY have one parent, one or more children and/or one or more permissions
 *      - A role has a permission if and only this role OR one of its children (at any level of deep) have this
 *        permission
 *      - Analogously, a role does not have a permission if its parent has it
 */
interface RoleInterface extends IteratorAggregate
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
     * Checks if a permission exists for this role or any child roles.
     *
     * @param  PermissionInterface|string $permission
     * @return bool
     */
    public function hasPermission($permission);

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
