<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions\Rbac;

use RecursiveIterator;

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
     * @param $name
     * @return RoleInterface
     */
    public function addPermission($name);

    /**
     * Checks if a permission exists for this role or any child roles.
     *
     * @param  string $name
     * @return bool
     */
    public function hasPermission($name);

    /**
     * Add a child.
     *
     * @param  RoleInterface|string $child
     * @return Role
     */
    public function addChild($child);

    /**
     * @param RoleInterface|string $parent
     * @return self
     */
    public function addParent($parent);

    /**
     * @param  RoleInterface $parent
     * @return self
     */
    public function setParent(RoleInterface $parent);

    /**
     * @return null|RoleInterface
     */
    public function getParent($name = null);

    /**
     * Return all parent roles
     *
     * @return array|\Traversable
     */
    public function getParents();

    /**
     * Check for the existence of a parent role
     *
     * @param string $name
     * @return bool
     */
    public function hasParent($name);
}
