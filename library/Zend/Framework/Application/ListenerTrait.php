<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\Manager\ListenerTrait as Listener;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * Pull listener from service manager
     *
     * @param $listener
     * @return mixed
     */
    public function listener($listener)
    {
        return $this->sm->get($listener);
    }

    /**
     *
     */
    public function run()
    {
        $event = new Event;

        $event->setServiceManager($this->sm);

        $this->__invoke($event);
    }
}
