<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver;

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\View\Resolver as ViewResolver;

class Factory implements FactoryInterface
{
    /**
     * @param ServiceManager $serviceLocator
     * @return mixed|ViewResolver\AggregateResolver
     */
    public function createService(ServiceManager $serviceLocator)
    {
        $resolver = new ViewResolver\AggregateResolver();
        $resolver->attach($serviceLocator->getService('View\Template\MapResolver'));
        $resolver->attach($serviceLocator->getService('View\Template\PathStack'));
        return $resolver;
    }
}
