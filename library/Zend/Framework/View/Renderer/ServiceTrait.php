<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\View\Renderer\RendererInterface;

trait ServiceTrait
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @return null|RendererInterface
     */
    public function renderer()
    {
        return $this->renderer;
    }

    /**
     * @param RendererInterface $renderer
     * @return self
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
}
