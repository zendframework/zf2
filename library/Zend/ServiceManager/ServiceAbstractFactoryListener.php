<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\ServiceListener;

class ServiceAbstractFactoryListener implements ServiceListenerInterface
{
    public function __invoke($event)
    {
        //$sm = $event->getTarget();

        $name = $event->getName();

        if (!isset($this->factory[$name])) {
            return false;
        }

        $factory = $this->factory[$name];

        return $factory($event);
    }
}
