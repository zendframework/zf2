<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password\Factory;

use Zend\Crypt\Password\HandlerAggregate;
use Zend\Crypt\Password\Options\PasswordHandlerAggregateOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory that instantiates {@see HandlerAggregate}.
 */
class HandlerAggregateFactory implements FactoryInterface
{
    /**
     * createService(): defined by FactoryInterface.
     *
     * @see    FactoryInterface::createService()
     * @param  ServiceLocatorInterface $serviceLocator
     * @return HandlerAggregate
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $aggregate = new HandlerAggregate();
        $aggregate->setHandlerManager($serviceLocator->get('Zend\Crypt\Password\HandlerManager'));

        $config = $serviceLocator->get('Zend\Crypt\Config');

        if (isset($config['password']['handler_aggregate'])) {
            $options = new PasswordHandlerAggregateOptions(
                $config['password']['handler_aggregate']
            );

            $aggregate->setOptions($options);
        }

        return $aggregate;
    }
}
