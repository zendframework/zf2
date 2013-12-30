<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver;

use Zend\Framework\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\View\Resolver\AggregateResolver as ViewResolver;

class Factory implements FactoryInterface
{
    /**
     * @param ServiceManager $serviceLocator
     * @return ViewResolver
     */
    public function createService(ServiceManager $serviceLocator)
    {
        $resolver = new ViewResolver;
        $resolver->attach($serviceLocator->service('View\Template\Resolver\Map'));
        $resolver->attach($serviceLocator->service('View\Template\Resolver\PathStack'));
        return $resolver;
    }
}
