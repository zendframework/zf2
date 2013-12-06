<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\ServiceRequest;

/**
 * Service Manage Interface
 */
interface ServiceManagerInterface
{
    /**
     * Retrieve a registered instance
     *
     * @param  ServiceRequest  $name
     * @throws Exception\ServiceNotFoundException
     * @return object|array
     */
    public function get(ServiceRequest $name);

    /**
     * Check for a registered instance
     *
     * @param  string|array  $name
     * @return bool
     */
    public function has($name);
}
