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

use Traversable;

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
     * Detach an event listener
     *
     * @param  callable $listener
     * @param  string $eventName optional to speed up process
     * @return bool
     */
    public function detach(callable $listener, $eventName = '')
    {
        // TODO: Implement detach() method.
    }

    /**
     * Detach a listener aggregate
     *
     * @param  ListenerAggregateInterface $aggregate
     * @return bool
     */
    public function detachAggregate(ListenerAggregateInterface $aggregate)
    {
        // TODO: Implement detachAggregate() method.
    }

    /**
     * Trigger an event (optionally until using a callback returns a boolean true)
     *
     * @param  string $eventName
     * @param  EventInterface|null $event
     * @param  callable|null $callback
     * @return ResponseCollection
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

                if ($event->isPropagationStopped() || ($callback && $callback($lastResponse))) {
                    $responseCollection = new ResponseCollection($responses);
                    $responseCollection->setStopped(true);

                    return $responseCollection;
                }
            }
        }

        return new ResponseCollection($responses);
    }

    /**
     * Get a list of event names for which this collection has listeners
     *
     * @return array
     */
    public function getEventNames()
    {
        return array_keys($this->events);
    }

    /**
     * Retrieve a list of listeners registered to a given event
     *
     * @param  string $eventName
     * @return array
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
     * Clear all listeners for a given event
     *
     * @param  string $eventName
     * @return void
     */
    public function clearListeners($eventName)
    {
        // TODO: Implement clearListeners() method.
    }

    /**
     * Set the identifiers (overrides any currently set identifiers)
     *
     * @param  array|Traversable $identifiers
     * @return void
     */
    public function setIdentifiers($identifiers)
    {
        // TODO: Implement setIdentifiers() method.
    }

    /**
     * Add some identifier(s) (appends to any currently set identifiers)
     *
     * @param  array|Traversable $identifiers
     * @return void
     */
    public function addIdentifiers($identifiers)
    {
        // TODO: Implement addIdentifiers() method.
    }

    /**
     * Get the identifier(s) for this EventManager
     *
     * @return array
     */
    public function getIdentifiers()
    {
        // TODO: Implement getIdentifiers() method.
    }
}
