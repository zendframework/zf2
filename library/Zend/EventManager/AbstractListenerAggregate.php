<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;


/**
 * Abstract aggregate listener
 */
abstract class AbstractListenerAggregate implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    protected $sharedListeners = array();

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }

        if (!empty($this->sharedListeners)) {
            $sharedEvents = $events->getSharedManager();

            foreach ($this->sharedListeners as $id => $listeners) {
                foreach ($listeners as $index => $callbacks) {
                    if (!is_array($callbacks)) {
                        $this->sharedListeners[$id][$index] = $callbacks = array($callbacks);
                    }

                    foreach ($callbacks as $subIndex => $callback) {
                        if ($sharedEvents->detach($id, $callback)) {
                            unset($this->sharedListeners[$id][$index][$subIndex]);
                        }
                    }
                }
            }
        }
    }
}
