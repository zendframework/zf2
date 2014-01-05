<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Framework\Service\ServicesTrait as Services;
use Zend\View\Renderer\RendererInterface as RendererInterface;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @return RendererInterface
     */
    public function renderer()
    {
        return $this->renderer;
    }

    /**
     * @param RendererInterface $renderer
     * @return $this
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
}
