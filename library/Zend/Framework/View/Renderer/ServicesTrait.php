<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\View\Renderer\RendererInterface as ViewRenderer;

trait ServicesTrait
{
    /**
     * @return null|ViewRenderer
     */
    public function viewRenderer()
    {
        return $this->sm->get('View\Renderer');
    }

    /**
     * @param ViewRenderer $renderer
     * @return self
     */
    public function setViewRenderer(ViewRenderer $renderer)
    {
        return $this->sm->add('View\Renderer', $renderer);
    }
}