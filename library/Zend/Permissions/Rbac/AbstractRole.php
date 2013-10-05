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

abstract class AbstractRole implements RoleInterface
{
    /**
     * @var null|RoleInterface
     */
    protected $parent;

    /**
     * @var array|RoleInterface[]
     */
    protected $children = array();

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
     * @param  string $name
     * @return void
     */
    public function addPermission($name)
    {
        $this->permissions[(string)$name] = true;
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

        $iteratorIterator = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iteratorIterator as $leaf) {
            /** @var RoleInterface $leaf */
            if ($leaf->hasPermission($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set parent role
     *
     * @param  RoleInterface $parent
     * @return void
     */
    public function setParent(RoleInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent role
     *
     * @return null|RoleInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add a child.
     *
     * @param  RoleInterface|string $child
     * @return void
     * @throws Exception\InvalidArgumentException
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
    }

    /**
     * Get children roles
     *
     * @return array|RoleInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Implement the IteratorAggregate interface
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new RecursiveArrayIterator($this->children);
    }
}
