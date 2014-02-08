<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\Manager\ManagerInterface;

interface ApplicationInterface
    extends ManagerInterface
{
    /**
     * Push listener to top of queue
     *
     * @param string $name
     * @param ListenerInterface $listener
     * @param int $priority
     * @return self
     */
    public function push($name, ListenerInterface $listener, $priority = self::PRIORITY);
}
