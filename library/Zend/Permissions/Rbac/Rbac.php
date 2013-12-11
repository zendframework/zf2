<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions\Rbac;

use RecursiveIteratorIterator;

/**
 * A RBAC container is just here to store roles and avoid any duplicate
 */
class Rbac
{
    /**
     * List of roles
     *
     * @var array|RoleInterface[]
     */
    protected $roles = [];

    /**
     * Add a role to the container
     *
     * @param  RoleInterface|string $role
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function addRole($role)
    {
        $roleName = (string) $role;

        // If the role is already registered we throw an exception, as it could be a potential security issue
        // to have two roles with same name
        if (isset($this->roles[$roleName])) {
            throw new Exception\InvalidArgumentException(sprintf(
                'A role with name "%s" already exists in the container',
                $roleName
            ));
        }

        if (is_string($role)) {
            $role = new Role($roleName);
        }

        $this->roles[$roleName] = $role;
    }

    /**
     * Is a role with the same name already registered?
     *
     * @param  RoleInterface|string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->getRole((string) $role) !== null;
    }

    /**
     * Get a role by its name
     *
     * @param  string $name
     * @return RoleInterface|null
     */
    public function getRole($name)
    {
        // First check the stored roles, as it's faster
        if (isset($this->roles[$name])) {
            return $this->roles[$name];
        }

        // Otherwise, we trigger a recursion on all the stored roles
        foreach ($this->roles as $role) {
            $iteratorIterator = new RecursiveIteratorIterator($role, RecursiveIteratorIterator::SELF_FIRST);

            /* @var RoleInterface $role */
            foreach ($iteratorIterator as $child) {
                $roleName = $child->getName();

                // We add it to the loaded roles, for faster retrieval in the future
                $this->roles[$roleName] = $child;

                if ($roleName === $name) {
                    return $child;
                }
            }
        }

        return null;
    }

    /**
     * Determines if access is granted by checking the role and child roles for permission.
     *
     * @param  RoleInterface|string             $role
     * @param  PermissionInterface|string       $permission
     * @param  AssertionInterface|Callable|null $assert
     * @throws Exception\InvalidArgumentException
     * @return bool
     */
    public function isGranted($role, $permission, $assert = null)
    {
        if (!$role instanceof RoleInterface) {
            $role = $this->getRole($role);
        }

        if ($assert) {
            if ($assert instanceof AssertionInterface) {
                if (!$assert->assert($this)) {
                    return false;
                }
            } elseif (is_callable($assert)) {
                if (!$assert($this)) {
                    return false;
                }
            } else {
                throw new Exception\InvalidArgumentException(
                    'Assertions must be a callable or an instance of Zend\Permissions\Rbac\AssertionInterface'
                );
            }
        }

        $permission = (string) $permission;

        // First check directly the role
        if ($role->hasPermission($permission)) {
            return true;
        }

        // Otherwise, we recursively check each children
        $iteratorIterator = new RecursiveIteratorIterator($role, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iteratorIterator as $child) {
            /** @var RoleInterface $child */
            if ($child->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
