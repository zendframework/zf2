<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\ModuleEvent;
use Zend\Framework\EventManager\Listener;
use Zend\Framework\EventManager\EventInterface;

/**
 * Module resolver listener
 */
class ModuleResolverListener extends Listener
{
    /**
     * @param  EventInterface $event
     * @return object|false False if module class does not exist
     */
    public function __invoke(EventInterface $event)
    {
        $moduleName = $event->getModuleName();
        $class      = $moduleName . '\Module';

        if (!class_exists($class)) {
            return false;
        }

        $module = new $class;
        return $module;
    }
}
