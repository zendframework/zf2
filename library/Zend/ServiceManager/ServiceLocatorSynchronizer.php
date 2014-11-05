<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

class ServiceLocatorSynchronizer implements SynchronizerInterface
{
    /**
     * A service name ready to synchronize
     *
     * @var string
     */
    protected $toSynchronize = array();
    
    /**
     * List of services that should be synchronized when a service is updated
     *
     * @var array
     */
    protected $synchronizedServices = array();


    /**
     * @param SynchronizedFactoryInterface|\SplObserver $observer
     */
    public function attach(\SplObserver $observer)
    {
        $services = $observer->getServices();
        if (!is_array($services)) {
            $services = array($services);
        }
        foreach ($services as $service) {
            if (empty($this->synchronizedServices[$service])) {
                $this->synchronizedServices[$service] = array();
            }
            $this->synchronizedServices[$service][] = $observer;
        }
    }

    /**
     * @param SynchronizedFactoryInterface|\SplObserver $observer
     */
    public function detach(\SplObserver $observer)
    {
        $services = $observer->getServices();
        if (!is_array($services)) {
            $services = array($services);
        }
        foreach ($services as $service) {
            if (empty($this->synchronizedServices[$service])) {
                continue;
            }

            $list = [];
            $factories = $this->synchronizedServices[$service];
            foreach ($factories as $factory) {
                if ($observer === $factory) {
                    continue;
                }
                $list[] = $factory;
            }
            $this->synchronizedServices[$service] = $list;
        }
    }

    /**
     * {@inheritDoc]
     */
    public function synchronize($name, $service)
    {
        $this->toSynchronize = array($name, $service);
        
        return $this;
    }

    /**
     * {@inheritDoc]
     */
    public function toSynchronize()
    {
        if (!$this->toSynchronize) {
            return null;
        }
        
        list($name, $service) = $this->toSynchronize;
        return $service;
    }

    /**
     * {@inheritDoc}
     */
    public function notify()
    {
        list($name, $service) = $this->toSynchronize;
        if (!empty($this->synchronizedServices[$name])) {
            /** @var \SplObserver[] $factories */
            $factories = $this->synchronizedServices[$name];
            foreach ($factories as $factory) {
                $factory->update($this);
            }
        }        
        
        $this->toSynchronize = array();
    }

    /**
     * @return array
     */
    public function getSynchronizedServices()
    {
        return $this->synchronizedServices;
    }
}
