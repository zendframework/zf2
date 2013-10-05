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
     * @param  string $name
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
     * Set parent role
     *
     * @param  RoleInterface $parent
     * @return void
     */
    public function setParent(RoleInterface $parent);

    /**
     * Get parent role
     *
     * @return null|RoleInterface
     */
    public function getParent();

    /**
     * Add a child.
     *
     * @param  RoleInterface|string $child
     * @return void
     */
    public function addChild($child);

    /**
     * Get children roles.
     *
     * @return array|RoleInterface[]
     */
    public function getChildren();
}
