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

class Role implements RoleInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array|RoleInterface[]
     */
    protected $children = [];

    /**
     * @var array|PermissionInterface
     */
    protected $permissions = [];

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function addPermission($permission)
    {
        $this->permissions[(string) $permission] = $permission;
    }

    /**
     * {@inheritDoc}
     */
    public function removePermission($permission)
    {
        unset($this->permissions[(string) $permission]);
    }

    /**
     * {@inheritDoc}
     */
    public function hasPermission($permission)
    {
        $name = (string) $permission;

        if (isset($this->permissions[$name])) {
            return true;
        }

        $iteratorIterator = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iteratorIterator as $child) {
            /** @var RoleInterface $child */
            if ($child->hasPermission($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function addChild(RoleInterface $child)
    {
        $this->children[$child->getName()] = $child;
    }

    /**
     * {@inheritDoc}
     */
    public function removeChild(RoleInterface $child)
    {
        unset($this->children[$child->getName()]);
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->name;
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
