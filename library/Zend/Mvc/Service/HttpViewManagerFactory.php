<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Mvc\View\Http\ViewManager as HttpViewManager;

class HttpViewManagerFactory implements FactoryInterface
{
    /**
     * Create and return a view manager for the HTTP environment
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return HttpViewManager
     */
    public function createService(ServiceManager $serviceLocator)
    {
        return new HttpViewManager();
    }

    public function __invoke(ServiceManager $serviceLocator)
    {
        return $this->createService($serviceLocator);
    }
}
