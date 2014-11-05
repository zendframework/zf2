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
    protected $toSynchronize;
    
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
        foreach ($services as $service) {
            if (empty($synchronizedServices[$service])) {
                $synchronizedServices[$service] = array();
            }
            $synchronizedServices[$service][] = $observer;
        }
    }

    /**
     * @param SynchronizedFactoryInterface|\SplObserver $observer
     */
    public function detach(\SplObserver $observer)
    {
        $services = $observer->getServices();
        foreach ($services as $service) {
            if (empty($synchronizedServices[$service])) {
                continue;
            }
            
            $list = [];
            $factories = $synchronizedServices[$service];
            foreach ($factories as $factory) {
                if ($observer === $factory) {
                    continue;
                }
                $list[] = $factory;
            }
            $synchronizedServices[$service] = $list;
        }
    }

    /**
     * {@inheritDoc]
     */
    public function synchronize($service)
    {
        $this->toSynchronize = $service;
        
        return $this;
    }

    /**
     * {@inheritDoc]
     */
    public function toSynchronize()
    {
        return $this->toSynchronize;
    }

    /**
     * {@inheritDoc}
     */
    public function notify()
    {
        if (!empty($this->synchronizedServices[$this->toSynchronize])) {
            /** @var \SplObserver[] $factories */
            $factories = $this->synchronizedServices[$this->toSynchronize];
            foreach ($factories as $factory) {
                $factory->update($this);
            }
        }        
        
        $this->toSynchronize = array();
    }
}
