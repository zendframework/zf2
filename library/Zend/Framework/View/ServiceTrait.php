<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\View\Renderer\ServiceTrait as ViewRenderer;
use Zend\Framework\View\Resolver\ServiceTrait as ViewResolver;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\Framework\View\Manager\ServiceTrait as ViewManager;

trait ServiceTrait
{
    /**
     *
     */
    use ViewManager,
        ViewModel,
        ViewRenderer,
        ViewResolver;

    /**
     * @var ListenerInterface
     */
    protected $view;

    /**
     * @return ListenerInterface
     */
    public function view()
    {
        return $this->view;
    }

    /**
     * @param ListenerInterface $view
     * @return self
     */
    public function setView(ListenerInterface $view)
    {
        $this->view = $view;
        return $this;
    }
}
