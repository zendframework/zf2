<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Permissions
 */

namespace ZendTest\Permissions\Acl\TestAsset\UseCase1;

use Zend\Permissions\Acl\Role;

class User implements Role\RoleInterface
{
    public $role = 'guest';
    public function getRoleId()
    {
        return $this->role;
    }
}
