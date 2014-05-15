<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Zend\EventManager;

/**
 * The fast event manager is a minimal, featured limited implementation of the event manager interface. It
 * does not contain any advanced features (like wildcard) and have no knowledge of shared manager. This
 * should be used for performance critical application, where you do not need flexibility of the shared
 * event manager
 */
class FastEventManager implements EventManagerInterface
{
    /**
     * Subscribed events and their listeners
     *
     * @var array
     */
    protected $events = [];

    /**
     * @var bool[]
     */
    protected $orderedByPriority = [];

    /**
     * {@inheritDoc}
     */
    public function attach($eventName, callable $listener, $priority = 1)
    {
        $this->events[$eventName][(int) $priority][] = $listener;
        $this->orderedByPriority[$eventName]         = false;

        return $listener;
    }

    /**
     * {@inheritDoc}
     */
    public function attachAggregate(ListenerAggregateInterface $aggregate, $priority = 1)
    {
        return $aggregate->attach($this, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function detach(callable $listener, $eventName = '')
    {
        if ($eventName !== null && isset($this->events[$eventName])) {
            foreach ($this->events[$eventName] as &$listeners) {
                if (($key = array_search($listener, $listeners, true)) !== false) {
                    unset($listeners[$key]);
                    return true;
                }
            }

            return false;
        }

        foreach ($this->events as &$event) {
            foreach ($event as &$listeners) {
                if (($key = array_search($listener, $listeners, true)) !== false) {
                    unset($listeners[$key]);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function detachAggregate(ListenerAggregateInterface $aggregate)
    {
        return $aggregate->detach($this);
    }

    /**
     * {@inheritDoc}
     */
    public function trigger($eventName, EventInterface $event = null, callable $callback = null)
    {
        // Initial value of stop propagation flag should be false
        $event = $event ?: new Event();
        $event->stopPropagation(false);

        $responses = [];
        $listeners = $this->getListeners($eventName);

        foreach ($listeners as $listenersByPriority) {
            foreach ($listenersByPriority as $listener) {
                $lastResponse = $listener($event);
                $responses[]  = $lastResponse;

                if (($callback && $callback($lastResponse) || $event->isPropagationStopped())) {
                    $responseCollection = new ResponseCollection($responses);
                    $responseCollection->setStopped(true);

                    return $responseCollection;
                }
            }
        }

        return new ResponseCollection($responses);
    }

    /**
     * {@inheritDoc}
     */
    public function getEventNames()
    {
        return array_keys($this->events);
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners($eventName)
    {
        if (isset($this->orderedByPriority[$eventName]) && !$this->orderedByPriority[$eventName]) {
            krsort($this->events[$eventName], SORT_NUMERIC);
            $this->orderedByPriority[$eventName] = true;
        }

        return isset($this->events[$eventName]) ? $this->events[$eventName] : [];
    }

    /**
     * {@inheritDoc}
     */
    public function clearListeners($eventName)
    {
        unset($this->events[$eventName]);
    }
}
