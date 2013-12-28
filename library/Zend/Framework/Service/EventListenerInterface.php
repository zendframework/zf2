<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

interface EventListenerInterface
{
    /**
     *
     */
    const EVENT_SERVICE = 'service';
    /**
     *
     */
    const FACTORY_OLD_INTERFACE = 'Zend\Framework\ServiceManager\FactoryInterface';
    const FACTORY_INTERFACE = 'Zend\Framework\Mvc\Service\ListenerFactoryInterface';
}
