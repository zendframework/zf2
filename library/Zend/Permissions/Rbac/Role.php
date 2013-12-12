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
     * @var int
     */
    protected $index = 0;

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
        return isset($this->permissions[(string) $permission]);
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * {@inheritDoc}
     */
    public function addChild(RoleInterface $child)
    {
        $this->children[] = $child;
    }

    /**
     * {@inheritDoc}
     */
    public function removeChild(RoleInterface $child)
    {
        if (($search = array_search($child, $this->children, true)) !== false) {
            unset($this->children[$search]);
        }
    }

    /*
     * --------------------------------------------------------------------------------
     * RecursiveIterator implementation
     * --------------------------------------------------------------------------------
     */

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->children[$this->index];
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return isset($this->children[$this->index]);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return $this->children[$this->index];
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return $this->valid() && $this->current() instanceof RecursiveIterator;
    }
}
