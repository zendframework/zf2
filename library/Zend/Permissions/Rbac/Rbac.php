<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions\Rbac;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Rbac
{
    /**
     * List of roles
     *
     * @var array|RoleInterface[]
     */
    protected $roles = array();

    /**
     * flag: whether or not to create roles automatically if they do not exist.
     *
     * @var bool
     */
    protected $createMissingRoles = false;

    /**
     * Set whether or not to create roles automatically if they do not exist
     *
     * @param  bool $createMissingRoles
     * @return void
     */
    public function setCreateMissingRoles($createMissingRoles)
    {
        $this->createMissingRoles = (bool) $createMissingRoles;
    }

    /**
     * Get whether or not to create roles automatically if they do not exist
     *
     * @return bool
     */
    public function getCreateMissingRoles()
    {
        return $this->createMissingRoles;
    }

    /**
     * Add a child
     *
     * @param  string|RoleInterface       $child
     * @param  array|RoleInterface[]|null $parents
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function addRole($child, $parents = null)
    {
        if (is_string($child)) {
            $child = new Role($child);
        }

        if (!$child instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Child must be a string or implement Zend\Permissions\Rbac\RoleInterface'
            );
        }

        if (null !== $parents) {
            if (!is_array($parents)) {
                $parents = array($parents);
            }

            foreach ($parents as $parent) {
                if ($this->createMissingRoles && !$this->hasRole($parent)) {
                    $this->addRole($parent);
                }

                $this->getRole($parent)->addChild($child);
            }
        }

        $this->roles[] = $child;
    }

    /**
     * Is a child with $name registered?
     *
     * @param  RoleInterface|string $objectOrName
     * @return bool
     */
    public function hasRole($objectOrName)
    {
        try {
            $this->getRole($objectOrName);
            return true;
        } catch (Exception\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Get a child
     *
     * @param  RoleInterface|string $objectOrName
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

        $iterator         = new RecursiveArrayIterator($this->roles);
        $iteratorIterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iteratorIterator as $leaf) {
            if ((is_string($objectOrName) && $leaf->getName() == $objectOrName) || $leaf == $objectOrName) {
                return $leaf;
            }
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'No child with name "%s" could be found',
            is_object($objectOrName) ? $objectOrName->getName() : $objectOrName
        ));
    }

    /**
     * Determines if access is granted by checking the role and child roles for permission.
     *
     * @param  RoleInterface|string             $role
     * @param  string                           $permission
     * @param  AssertionInterface|Callable|null $assert
     * @throws Exception\InvalidArgumentException
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

        return $this->getRole($role)->hasPermission($permission);
    }
}
