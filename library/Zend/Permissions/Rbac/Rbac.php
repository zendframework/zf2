<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions\Rbac;

use RecursiveIteratorIterator;

class Rbac extends AbstractIterator
{
    /**
     * flag: whether or not to create roles automatically if
     * they do not exist.
     *
     * @var        bool
     * @deprecated 2.2.0 This is needless, since hasRole(), getRole() and IsGranted()
     *             will look for roles recursively in children's children
     */
    protected $createMissingRoles = false;

    /**
     * Add a child role.
     *
     * @deprecated 2.2.0
     * @param string|RoleInterface $role
     * @param array|RoleInterface|null $parents
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function addRole($role, $parents = null)
    {
        trigger_error(
            'This method will be removed in the future. Please use addRoleWithParents() instead',
            E_USER_DEPRECATED
        );

        $this->addRoleWithParents($role, $parents);
        return $this;
    }

    /**
     * Add a child with children.
     *
     * @param string|RoleInterface $role
     * @param array|RoleInterface|null $children
     * @return self
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RoleNotFoundException
     */
    public function addRoleWithChildren($role, $children = null)
    {
        if (is_string($role)) {
            $role = new Role($role);
        }

        if (!$role instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Child must be a string or implement Zend\Permissions\Rbac\RoleInterface'
            );
        }

        if ($children) {
            if (!is_array($children)) {
                $children = array($children);
            }

            foreach ($children as $child) {
                if (is_string($child) && $this->hasRole($child)) {
                    // prefer defined roles to ensure defined permissions are used
                    $child = $this->getRole($child);
                }

                $role->addChild($child);
            }
        }

        $this->children[] = $role;
        return $this;
    }

    /**
     * Add a child role and its parents
     *
     * @todo clearify if createMissingRoles still makes sense
     * @param string|RoleInterface $role
     * @param array|RoleInterface|null $parents
     * @return self
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RoleNotFoundException
     */
    public function addRoleWithParents($role, $parents = null)
    {
        if (is_string($role)) {
            $role = new Role($role);
        }

        if (!$role instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Child must be a string or implement Zend\Permissions\Rbac\RoleInterface'
            );
        }

        $this->children[] = $role;

        if ($parents) {
            if (!is_array($parents)) {
                $parents = array($parents);
            }

            foreach ($parents as $parent) {
                if ($this->hasRole($parent)) {
                    // prefer already defined roles
                    $this->getRole($parent)->addChild($role);
                    continue;
                }

                if (!$this->createMissingRoles) {
                    throw new Exception\RoleNotFoundException(sprintf(
                        'Could not find parent role "%s"',
                        ($parent instanceof RoleInterface)? $parent->getName() : $parent
                    ));
                }

                $this->addRoleWithChildren($parent, $role);
            }
        }

        return $this;
    }

    /**
     * Is a child with $name registered?
     *
     * @param  \Zend\Permissions\Rbac\RoleInterface|string $objectOrName
     * @return bool
     */
    public function hasRole($objectOrName)
    {
        try {
            $this->getRole($objectOrName);
            return true;
        } catch (Exception\RoleNotFoundException $e) {
            return false;
        }
    }

    /**
     * Get a child.
     *
     * @param  \Zend\Permissions\Rbac\RoleInterface|string $objectOrName
     * @return RoleInterface
     * @throws Exception\InvalidArgumentException
     */
    public function getRole($objectOrName)
    {
        if (!is_string($objectOrName) && !$objectOrName instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Expected string or implement \Zend\Permissions\Rbac\RoleInterface'
            );
        }

        $it = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $leaf) {
            if ((is_string($objectOrName) && $leaf->getName() == $objectOrName) || $leaf === $objectOrName) {
                return $leaf;
            }
        }

        throw new Exception\RoleNotFoundException(
            sprintf(
                'No child with name "%s" could be found',
                is_object($objectOrName) ? $objectOrName->getName() : $objectOrName
            )
        );
    }

    /**
     * Determines if access is granted by checking the role and child roles for permission.
     *
     * @param  RoleInterface|string             $role
     * @param  string                           $permission
     * @param  AssertionInterface|Callable|null $assert
     * @return bool
     */
    public function isGranted($role, $permission, $assert = null)
    {
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
                    'Assertions must be a Callable or an instance of Zend\Permissions\Rbac\AssertionInterface'
                );
            }
        }

        if ($this->getRole($role)->hasPermission($permission)) {
            return true;
        }

        return false;
    }

    /**
     * @param      boolean                     $createMissingRoles
     * @return     \Zend\Permissions\Rbac\Rbac
     * @deprecated 2.2.0
     */
    public function setCreateMissingRoles($createMissingRoles)
    {
        trigger_error(
            'This method does not make any effect and will be removed in the future.',
            E_USER_DEPRECATED
        );

        $this->createMissingRoles = $createMissingRoles;

        return $this;
    }

    /**
     * @return     boolean
     * @deprecated 2.2.0
     */
    public function getCreateMissingRoles()
    {
        trigger_error(
            'This method does not make any effect and will be removed in the future.',
            E_USER_DEPRECATED
        );

        return $this->createMissingRoles;
    }
}
