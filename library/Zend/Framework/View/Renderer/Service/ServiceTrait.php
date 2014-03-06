<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer\Service;

use Zend\View\Renderer\RendererInterface as ViewRenderer;

trait ServiceTrait
{
    /**
     * @var ViewRenderer
     */
    protected $renderer;

    /**
     * @return null|ViewRenderer
     */
    public function viewRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param ViewRenderer $renderer
     * @return self
     */
    public function setViewRenderer(ViewRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
}
