<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Zend\Framework\Event\Config\ConfigInterface;
use Zend\Framework\Event\EventInterface;

trait ServicesTrait
{
    /**
     * @return ManagerInterface
     */
    public function eventManager()
    {
        return $this->sm->get('EventManager');
    }

    /**
     * @return ConfigInterface
     */
    public function listeners()
    {
        return $this->eventManager()->listeners();
    }

    /**
     * @param ManagerInterface $em
     * @return self
     */
    public function setEventManager(ManagerInterface $em)
    {
        $this->sm->add('EventManager', $em);
        return $this;
    }

    /**
     * @param string|EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function trigger($event, $options = null)
    {
        return $this->eventManager()->trigger($event, $options);
    }
}
