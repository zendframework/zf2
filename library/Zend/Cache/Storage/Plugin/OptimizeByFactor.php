<?php

namespace Zend\Cache\Storage\Plugin;

use Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\PostEvent,
    Zend\Cache\InvalidArgumentAxception,
    Zend\Cache\LogicException,
    Zend\EventManager\EventCollection;

class OptimizeByFactor implements Plugin
{

    protected $handles = array();

    /**
     * Automatic optimizing factor
     *
     * @var int
     */
    protected $_optimizingFactor = 0;

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        foreach ($options as $name => $value) {
            $m = 'set' . str_replace('_', '', $name);
            $this->$m($value);
        }
    }

    public function getOptions()
    {
        return array(
            'optimizing_factor' => $this->getOptimizingFactor()
        );
    }

    /**
     * Get automatic optimizing factor
     *
     * @return int
     */
    public function getOptimizingFactor()
    {
        return $this->_optimizingFactor;
    }

    /**
     * Set automatic optimizing factor
     *
     * @param int $factor
     * @return Zend\Cache\Storage\Plugin\AutomaticOptimize
     */
    public function setOptimizingFactor($factor)
    {
        $factor = (int)$factor;
        if ($factor < 0) {
            throw new InvalidArgumentAxception("Invalid optimizing factor '{$factor}': must be greater or equal 0");
        }
        $this->_optimizingFactor = $factor;

        return $this;
    }

    public function attach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (isset($this->handles[$index])) {
            throw new LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        $handles[] = $eventCollection->attach('removeItem.post',       array($this, 'optimizeByFactor'));
        $handles[] = $eventCollection->attach('removeItems.post',      array($this, 'optimizeByFactor'));
        $handles[] = $eventCollection->attach('clear.post',            array($this, 'optimizeByFactor'));
        $handles[] = $eventCollection->attach('clearByNamespace.post', array($this, 'optimizeByFactor'));

        return $this;
    }

    public function detach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (!isset($this->handles[$index])) {
            throw new LogicException('Plugin not attached');
        }

        // detach all handles of this index
        foreach ($this->handles[$index] as $handle) {
            $eventCollection->detach($handle);
        }

        // remove all detached handles
        unset($this->handles[$index]);

        return $this;
    }

    /**
     * Optimize by factor on a success _RESULT_
     *
     * @param Zend\Cache\Storage\PostEvent $event
     */
    public function optimizeByFactor(PostEvent $event)
    {
        $factor = $this->getOptimizingFactor();
        if ($factor && $event->getResult() && mt_rand(1, $factor) == 1) {
            $params = $event->getParams();
            $event->getStorage()->optimize($params['options']);
        }
    }

}
