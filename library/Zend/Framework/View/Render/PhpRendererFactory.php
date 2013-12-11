<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Render;

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\Config;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Manager as ViewManager;

class PhpRendererFactory implements FactoryInterface
{
    public function createService(ServiceManager $sm)
    {
        $viewModel, $resolver, $pm;

        $renderer = new ViewPhpRenderer;
        $renderer->setHelperPluginManager($pm);
        $renderer->setResolver($resolver);

        $modelHelper = $renderer->plugin('viewmodel');
        $modelHelper->setRoot($viewModel);

        $view = new View(new Config($sm->get(new ServiceRequest('ApplicationConfig'))['view_manager']));
        $view->setEventManager($sm->get(new ServiceRequest('EventManager')));
        return $view;
    }
}
