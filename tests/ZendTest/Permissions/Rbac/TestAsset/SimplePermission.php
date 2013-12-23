<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Permissions\Rbac\TestAsset;

use Zend\Permissions\Rbac\PermissionInterface;
use Zend\Permissions\Rbac\RoleInterface;

class SimplePermission implements PermissionInterface
{
    /**
     * Get the permission name
     *
     * @return string|int
     */
    public function getName()
    {
        return 'simple';
    }

    /**
     * Get roles associated with the permission
     *
     * @return string|string[]|RoleInterface|RoleInterface[]
     */
    public function getRoles()
    {
        return 'role';
    }
}
