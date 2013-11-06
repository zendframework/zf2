<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Permissions;

/**
 * Common identity interface for RBAC and ACL models
 */
interface IdentityInterface 
{
    /**
     * Get the list of roles of this identity
     *
     * @return string|array|\Zend\Permissions\Acl\Role\RoleInterface|\Zend\Permissions\Rbac\RoleInterface
     */
    public function getRoles();
}
