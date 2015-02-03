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
 * EventManager
 */
class EventManager implements EventManagerInterface
{
    /**
     * An array that map event name to a list of listeners
     *
     * @var array
     */
    private $events = [];

    /**
     * @var ListenerPluginManager
     */
    private $listenerPluginManager;

    /**
     * @param ListenerPluginManager $listenerPluginManager
     */
    public function __construct(ListenerPluginManager $listenerPluginManager = null)
    {
        if (null === $listenerPluginManager) {
            $listenerPluginManager = new ListenerPluginManager();
        }

        $this->listenerPluginManager = $listenerPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function attach($eventName, $callbackOrSpec, $priority = 1)
    {
        // Each listener is encoded within an array, where the first parameter is the callback or spec,
        // and second parameter is a marker that is "true" if it is a lazy event, or false otherwise. The '.0'
        // is a hack so that array_merge_recursive preserve the keys
        $this->events[$eventName][((int) $priority) . '.0'][] =
            [$callbackOrSpec, is_array($callbackOrSpec) && is_string($callbackOrSpec[0])];

        return $callbackOrSpec;
    }

    /**
     * {@inheritDoc}
     */
    public function detach($eventName, $callbackOrSpec = null)
    {
        if (!isset($this->events[$eventName])) {
            return false;
        }

        // If no callback or spec, we remove all the listeners from a given event name
        if (null === $callbackOrSpec) {
            unset($this->events[$eventName]);
            return true;
        }

        // Otherwise, the operation is a bit more heavy
        $found = false;

        foreach ($this->events[$eventName] as &$listenersByPriority) {
            $key = array_search($callbackOrSpec, $listenersByPriority, true);

            if ($key !== false) {
                unset($listenersByPriority[$key]);
                $found = true;
            }
        }

        return $found;
    }

    /**
     * {@inheritDoc}
     */
    public function trigger($eventName, EventInterface $event)
    {
        $responses = [];

        foreach ($this->getListeners($eventName) as $listener) {
            $responses[] = $listener($event);

            if ($event->isPropagationStopped()) {
                break;
            }
        }

        return new ResponseCollection($responses);
    }

    /**
     * {@inheritDoc}
     */
    public function triggerUntil($eventName, EventInterface $event, callable $callback)
    {
        $responses = [];

        foreach ($this->getListeners($eventName) as $listener) {
            $latestResponse = $listener($event);
            $responses[]    = $latestResponse;

            if ($event->isPropagationStopped() || $callback($latestResponse)) {
                break;
            }
        }

        return new ResponseCollection($responses);
    }

    /**
     * @param  string $eventName
     * @return callable[]
     */
    private function getListeners($eventName)
    {
        // @TODO: PHP7 optimization: we can use the coalesce operator here
        $listeners = array_merge_recursive(
            isset($this->events[$eventName]) ? $this->events[$eventName] : [],
            isset($this->events['*']) ? $this->events['*'] : []
        );

        // @TODO: consider caching the sort for performance. However, as events are typically triggered only
        // one or two times, the extra work of caching may not be worth the work, especially on small datasets
        krsort($listeners, SORT_NUMERIC);

        foreach ($listeners as $priority => $listenersByPriority) {
            foreach ($listenersByPriority as list($listener, $isLazy)) {
                if ($isLazy) {
                    $listener[0] = $this->listenerPluginManager->get($listener[0]);

                    // @TODO: to benchmark: should we pass listenersByPriority be reference, and modify the "isLazy"
                    // to false to avoid a new call to plugin manager later?
                }

                yield $listener;
            }
        }
    }
}