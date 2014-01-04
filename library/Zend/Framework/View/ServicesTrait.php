<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\View\Listener as View;
use Zend\Framework\View\Service\ServicesTrait as ViewPlugin;
use Zend\Framework\View\Renderer\ServicesTrait as ViewRenderer;
use Zend\Framework\View\Resolver\ServicesTrait as ViewResolver;
use Zend\Framework\View\Model\ServicesTrait as ViewModel;
use Zend\Framework\View\Manager\ServicesTrait as ViewManager;

trait ServicesTrait
{
    /**
     *
     */
    use ViewPlugin, ViewRenderer, ViewResolver, ViewManager, ViewModel;

    /**
     * @return bool|View
     */
    public function view()
    {
        return $this->service('View');
    }

    /**
     * @param View $view
     * @return self
     */
    public function setView(View $view)
    {
        return $this->add('View', $view);
    }
}
