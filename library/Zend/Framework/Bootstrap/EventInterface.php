<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManager;

interface EventInterface
    extends Event
{
    /**
     * @param ServiceManager $sm
     * @return self
     */
    public function setServiceManager(ServiceManager $sm);
}
