<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\View\Plugin\ServicesTrait as ViewPluginManager;
use Zend\Framework\View\Model\ServicesTrait as ViewModel;
use Zend\Framework\View\Resolver\ServicesTrait as ViewResolver;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class RendererFactory
    extends Factory
{
    /**
     *
     */
    use ViewPluginManager,
        ViewModel,
        ViewResolver;

    /**
     * @param Request $request
     * @param array $options
     * @return Renderer
     */
    public function __invoke(Request $request, array $options = [])
    {
        $renderer = (new Renderer())->setViewPluginManager($this->viewPluginManager())
                                    ->setResolver($this->viewResolver());

        $modelHelper = $renderer->plugin('viewmodel');

        $modelHelper->setRoot($this->rootViewModel());

        return $renderer;
    }
}
