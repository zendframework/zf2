<?php

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
     * @param bool $autoAddDefaultProvider  Set the default Resolver at low priority
     */
    public function __construct($autoAddDefaultProvider = false)
    {
        $this->queue = new PriorityQueue;

        if (true === $autoAddDefaultProvider) {
            $this->queue->insert(new PrototypeProvider, -PHP_INT_MAX);
        }
    }

    /**
     * @return ProviderInterface[]|PriorityQueue
     */
    public function getResolvers()
    {
        return $this->queue;
    }

    /**
     * Add multiples resolvers at once
     *
     * @param array|\Traversable $providers  The set of resolvers. If the item is an array, we treat it as follow:
     *                                       $resolver[0] = ResolverInterface
     *                                       $resolver[1] = (int) Priority
     */
    public function addProviders($providers)
    {
        foreach ($providers as $provider) {
            is_array($provider) ? list($provider, $priority) = $provider : $priority = 0;
            $this->addProvider($provider, $priority);
        }
    }

    /**
     * Add a resolver to the queue
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
     * remove the given resolver from queue
     *
     * @param ProviderInterface $provider
     * @return $this
     */
    public function removeProvider(ProviderInterface $provider)
    {
        $this->queue->remove($provider);
        return $this;
    }

    /**
     * @param $eventName
     * @param $context
     * @param array $parameters
     * @throws \Zend\EventManager\Exception\RuntimeException
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName, $context = null, $parameters = null)
    {
        foreach ($this->queue as $provider) {
            $event = $provider->get($eventName, $context, $parameters);
            if ($event instanceof EventInterface) {
                return $event;
            }
        }
    }
}