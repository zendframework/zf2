<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\HelperFactory;

use Zend\View\HelperPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\BasePath as BasePathHelper;

/**
 * Class BasePathFactory
 */
class BasePathFactory implements FactoryInterface
{
    /**
     * Create the base path view helper
     *
     * @param ServiceLocatorInterface|HelperPluginManager $helperManager
     *
     * @return \Zend\View\Helper\BasePath
     */
    public function createService(ServiceLocatorInterface $helperManager)
    {
        $sl     = $helperManager->getServiceLocator();
        $config = $sl->has('Config') ? $sl->get('Config') : array();

        $helper = new BasePathHelper();

        if (isset($config['view_manager']) && isset($config['view_manager']['base_path'])) {
            $basePath = $config['view_manager']['base_path'];
        } else {
            $basePath = $sl->get('Request')->getBasePath();
        }

        return $helper->setBasePath($basePath);
    }
}
