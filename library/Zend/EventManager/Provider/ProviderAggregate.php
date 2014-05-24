<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager\Provider;

use Zend\EventManager\EventInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * Add Capability to the interface to run multiples and prioritized resolvers
 */
class ProviderAggregate implements ProviderInterface
{
    /**
     * @var PriorityQueue|ProviderInterface[]
     */
    protected $queue;

    /**
     * Adding the default provider in the queue will guaranty that a \Zend\EventManager\EventInterface is always
     * returned by the aggregator. It provides a reasonable fallback for the event dispatch process.
     *
     * @param bool $autoAddDefaultProvider  Set the default Resolver at low priority
     */
    public function __construct($autoAddDefaultProvider = false)
    {
        $this->queue = new PriorityQueue;

        if (true === $autoAddDefaultProvider) {
            $this->queue->insert(new DefaultProvider, -PHP_INT_MAX);
        }
    }

    /**
     * @return ProviderInterface[]|PriorityQueue
     */
    public function getProviders()
    {
        return $this->queue;
    }

    /**
     * Add multiples providers at once
     *
     * @param array|\Traversable $providers The set of resolvers. If the item is an array, we treat it as follow:
     *                                       $resolver[0] = ResolverInterface
     *                                       $resolver[1] = (int) Priority
     * @return $this
     */
    public function addProviders($providers)
    {
        foreach ($providers as $provider) {
            is_array($provider) ? list($provider, $priority) = $provider : $priority = 0;
            $this->addProvider($provider, $priority);
        }
        return $this;
    }

    /**
     * Add a resolver to the queue.
     *
     * @param ProviderInterface $provider
     * @param int $priority
     * @return $this
     */
    public function addProvider(ProviderInterface $provider, $priority = 0)
    {
        $this->queue->insert($provider, $priority);
        return $this;
    }

    /**
     * remove the given provider from queue.
     * Following PriorityQueue doc, only the first matched provider will be removed.
     *
     * @param ProviderInterface $provider
     * @return bool                         True on success, false otherwise
     */
    public function removeProvider(ProviderInterface $provider)
    {
        return $this->queue->remove($provider);
    }

    /**
     * Loop through each providers registered in queue. Once an EventInterface is returned by one of the providers,
     * the method will return it instantly, skipping next providers in queue.
     *
     * @param $eventName
     * @param $target
     * @param array $parameters
     * @throws \Zend\EventManager\Exception\RuntimeException
     * @return \Zend\EventManager\EventInterface|void
     */
    public function get($eventName, $target = null, $parameters = array())
    {
        foreach ($this->queue as $provider) {
            $event = $provider->get($eventName, $target, $parameters);
            if ($event instanceof EventInterface) {
                return $event;
            }
        }
    }
}
