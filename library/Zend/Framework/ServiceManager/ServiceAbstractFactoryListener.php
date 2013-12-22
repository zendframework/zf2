<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\ServiceRequestInterface as ServiceRequest;
use Zend\Framework\ServiceManager\ServiceListenerInterface as ServiceListener;

class ServiceAbstractFactoryListener
    implements ServiceListener
{
    /**
     * @param ServiceRequest $service
     * @return bool
     */
    public function __invoke(ServiceRequest $service)
    {
        //$sm = $service->getTarget();

        $name = $service->getName();

        if (!isset($this->factory[$name])) {
            return false;
        }

        $factory = $this->factory[$name];

        return $factory($service);
    }
}
