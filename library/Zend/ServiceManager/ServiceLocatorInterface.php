<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

/**
 * Service locator interface
 */
interface ServiceLocatorInterface
{
    /**
     * Retrieve a registered instance
     *
     * @param  string|ServiceRequestInterface $serviceRequest
     *
     * @return object|array
     *
     * @throws Exception\ServiceNotFoundException
     */
    public function get($serviceRequest);

    /**
     * Check for a registered instance
     *
     * @param  string|ServiceRequestInterface $serviceRequest
     *
     * @return bool
     */
    public function has($serviceRequest);
}
