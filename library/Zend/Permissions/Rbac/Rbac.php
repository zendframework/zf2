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

class Rbac extends AbstractIterator
{
    /**
     * flag: whether or not to create roles automatically if
     * they do not exist.
     *
     * @var bool
     */
    protected $createMissingRoles = false;

    /**
     * A list of assertions
     *
     * @var array|AssertionInterface[]
     */
    protected $assertions = array();

    /**
     * @param  bool                     $createMissingRoles
     * @return \Zend\Permissions\Rbac\Rbac
     */
    public function setCreateMissingRoles($createMissingRoles)
    {
        $this->createMissingRoles = $createMissingRoles;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCreateMissingRoles()
    {
        return $this->createMissingRoles;
    }

    /**
     * Register an assertion for a given permission
     *
     * If an assertion already exists for this permission, it overrides it
     *
     * @param  string                      $permission
     * @param  callable|AssertionInterface $assertion
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function registerAssertion($permission, $assertion)
    {
        if (!is_callable($assertion) && !$assertion instanceof AssertionInterface) {
            throw new Exception\InvalidArgumentException(
                'Assertions must be a Callable or an instance of Zend\Permissions\Rbac\AssertionInterface'
            );
        }

        $this->assertions[$permission] = $assertion;
    }

    /**
     * Does the container already contains an assertion for this permission?
     *
     * @param  string $permission
     * @return bool
     */
    public function hasAssertion($permission)
    {
        return isset($this->assertions[$permission]);
    }

    /**
     * Add a child.
     *
     * @param  string|RoleInterface               $child
     * @param  array|RoleInterface|null           $parents
     * @return self
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

        if ($parents) {
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

        $this->children[] = $child;

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
        } catch (Exception\InvalidArgumentException $e) {
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
     * If an assertion is already registered for the given permission, and if one is specified as a third
     * argument, it will use this one instead
     *
     * @param  RoleInterface|string             $role
     * @param  string                           $permission
     * @param  AssertionInterface|callable|null $assert
     * @throws Exception\InvalidArgumentException
     * @return bool
     */
    public function isGranted($role, $permission, $assert = null)
    {
        $assertion = null;

        if ($this->hasAssertion($permission)) {
            $assertion = $this->assertions[$permission];
        }

        $assertion = $assert ?: $assertion;

        if (null !== $assertion) {
            if ($assertion instanceof AssertionInterface) {
                if (!$assertion->assert($this)) {
                    return false;
                }
            } elseif (is_callable($assertion)) {
                if (!$assertion($this)) {
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
}
