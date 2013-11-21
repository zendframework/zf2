<?php
/**
 * Created by PhpStorm.
 * User: ocramius
 * Date: 21/11/13
 * Time: 18:18
 */

namespace Zend\ServiceManager;


interface ConfigurableFactoryInterface
{
    /**
     * Factory that allows instantiation of a service with some parameters passed in
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param ServiceRequestInterface $serviceRequest
     *
     * @return mixed
     */
    public function createService(
        ServiceLocatorInterface $serviceLocator,
        ServiceRequestInterface $serviceRequest
    );
} 