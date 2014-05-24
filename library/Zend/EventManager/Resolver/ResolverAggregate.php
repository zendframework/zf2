<?php

namespace Zend\EventManager\Resolver;

use Zend\EventManager\EventInterface;
use Zend\EventManager\Exception\RuntimeException;
use Zend\Stdlib\PriorityQueue;

/**
 * Add Capability to the interface to run multiples and prioritized resolvers
 */
class ResolverAggregate implements ResolverInterface
{
    /**
     * @var PriorityQueue|ResolverInterface[]
     */
    protected $queue;

    /**
     * @param bool $autoAddDefaultResolver  Set the default Resolver at low priority
     */
    public function __construct($autoAddDefaultResolver = false)
    {
        $this->queue = new PriorityQueue;

        if (true === $autoAddDefaultResolver) {
            $this->queue->insert(new PrototypeResolver, -PHP_INT_MAX);
        }
    }

    /**
     * @return ResolverInterface[]|PriorityQueue
     */
    public function getResolvers()
    {
        return $this->queue;
    }

    /**
     * Add multiples resolvers at once
     *
     * @param array|\Traversable $resolvers  The set of resolvers. If the item is an array, we treat it as follow:
     *                                       $resolver[0] = ResolverInterface
     *                                       $resolver[1] = (int) Priority
     */
    public function addResolvers($resolvers)
    {
        foreach ($resolvers as $resolver) {
            is_array($resolver) ? list($resolver, $priority) = $resolver : $priority = 0;
            $this->addResolver($resolver, $priority);
        }
    }

    /**
     * Add a resolver to the queue
     *
     * @param ResolverInterface $resolver
     * @param int $priority
     * @return $this
     */
    public function addResolver(ResolverInterface $resolver, $priority = 0)
    {
        $this->queue->insert($resolver, $priority);
        return $this;
    }

    /**
     * remove the given resolver from queue
     *
     * @param ResolverInterface $resolver
     * @return $this
     */
    public function removeResolver(ResolverInterface $resolver)
    {
        $this->queue->remove($resolver);
        return $this;
    }

    /**
     * @param $eventName
     * @throws \Zend\EventManager\Exception\RuntimeException
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName)
    {
        foreach ($this->queue as $resolver) {
            $event = $resolver->get($eventName);
            if ($event instanceof EventInterface) {
                return $event;
            }
        }
        throw new RuntimeException(sprintf('Cannot create an event for "%s"',
            is_object($eventName) ? get_class($eventName) : $eventName
        ));
    }
}