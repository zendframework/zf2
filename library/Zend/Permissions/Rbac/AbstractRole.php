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

abstract class AbstractRole extends AbstractIterator implements RoleInterface
{
    /**
     * @var null|RoleInterface
     */
    protected $parents;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $permissions = array();

    /**
     * Get the name of the role.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add permission to the role.
     *
     * @param $name
     * @return RoleInterface
     */
    public function addPermission($name)
    {
        $this->permissions[$name] = true;

        return $this;
    }

    /**
     * Checks if a permission exists for this role or any child roles.
     *
     * @param  string $name
     * @return bool
     */
    public function hasPermission($name)
    {
        if (isset($this->permissions[$name])) {
            return true;
        }

        $it = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $leaf) {
            /** @var RoleInterface $leaf */
            if ($leaf->hasPermission($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a child.
     *
     * @param  RoleInterface|string $child
     * @return Role
     */
    public function addChild($child)
    {
        if (is_string($child)) {
            $child = new Role($child);
        }
        if (!$child instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Child must be a string or implement Zend\Permissions\Rbac\RoleInterface'
            );
        }

        $child->setParent($this);
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param  RoleInterface $parent
     * @return RoleInterface
     */
    public function setParent(RoleInterface $parent)
    {
        $this->parents[$parent->getName()] = $parent;
        return $this;
    }

    /**
     * @param RoleInterface|string|null $name
     * @return null|RoleInterface
     */
    public function getParent($name = null)
    {
        if ($name === null) {
            return reset($this->parents);
        }

        if (!$this->hasParent($name)) {
            return null;
        }

        return $this->parents[$name];
    }

    /**
     * @see \Zend\Permissions\Rbac\RoleInterface::addParent()
     * @param string|RoleInterface
     * @return self
     */
    public function addParent($parent)
    {
        if (is_string($parent)) {
            if (isset($this->parents[$parent])) {
                $parent = $this->parents[$parent];
            } else {
                $parent = new Role($parent);
            }
        }

        if (!$parent instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Parent must be a string or implement Zend\Permissions\Rbac\RoleInterface'
            );
        }

        $this->parents[$parent->getName()] = $parent;
        $parent->addChild($this);

        return $this;
    }

    /**
     * @see \Zend\Permissions\Rbac\RoleInterface::getParents()
     * @return array|\Traversable
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @see \Zend\Permissions\Rbac\RoleInterface::hasParent()
     * @param string|RoleInterface $name
     * @return bool
     */
    public function hasParent($name)
    {
        if ($name instanceof RoleInterface) {
            $name = $name->getName();
        }

        return isset($this->parents[$name]);
    }
}
