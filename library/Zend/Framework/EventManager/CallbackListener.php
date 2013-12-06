<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\EventManager\CallbackListenerInterface;
use Zend\Framework\EventManager\ListenerInterface;

class CallbackListener extends EventListener implements CallbackListenerInterface
{
    /**
     * @var callback
     */
    protected $callback;

    /**
     * @param $callback
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($callback, $event = null, $target = null, $priority = null)
    {
        $this->setCallback($callback);

        parent::__construct($event, $target, $priority);
    }

    /**
     * @param $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        return call_user_func($this->callback, $event);
    }
}
