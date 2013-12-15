<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Render;

use Zend\Framework\ServiceManager\Request;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\View\View;
use Zend\View\Renderer\PhpRenderer as ViewRenderer;
use Zend\Framework\ServiceManager\FactoryInterface;

class PhpRenderFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return View
     */
    public function createService(ServiceManager $sm)
    {
        $renderer = new ViewRenderer();

        $renderer->setHelperPluginManager($sm->getViewPluginManager());
        $renderer->setResolver($sm->getViewResolver());

        $modelHelper = $renderer->plugin('viewmodel');
        $modelHelper->setRoot($sm->getViewModel());

        return $renderer;
    }
}
