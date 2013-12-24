<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;

class RendererFactory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return Renderer
     */
    public function createService(ServiceManager $sm)
    {
        $renderer = new Renderer();

        $renderer->setPluginManager($sm->getViewPluginManager());
        $renderer->setResolver($sm->getViewResolver());

        $modelHelper = $renderer->plugin('viewmodel');
        $modelHelper->setRoot($sm->getViewModel());

        return $renderer;
    }
}
