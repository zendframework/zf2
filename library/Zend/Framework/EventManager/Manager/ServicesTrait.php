<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\Manager;

trait ServicesTrait
{
    /**
     * @return ListenerInterface
     */
    public function eventManager()
    {
        return $this->service('EventManager');
    }

    /**
     * @param ListenerInterface $em
     * @return self
     */
    public function setEventManager(ListenerInterface $em)
    {
        $this->add('EventManager', $em);
        return $this;
    }
}
