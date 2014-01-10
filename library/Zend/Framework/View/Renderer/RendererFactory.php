<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\View\ServicesTrait as View;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;

class RendererFactory
    extends FactoryListener
{
    /**
     *
     */
    use View;

    /**
     * @param EventInterface $event
     * @return Renderer
     */
    public function __invoke(EventInterface $event)
    {
        $renderer = new Renderer();

        $renderer->setViewManager($this->viewManager());
        $renderer->setResolver($this->viewResolver());

        $modelHelper = $renderer->plugin('viewmodel');
        $modelHelper->setRoot($this->viewModel());

        return $renderer;
    }
}
