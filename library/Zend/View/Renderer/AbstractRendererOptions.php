<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

use Zend\Stdlib\AbstractOptions;
use Zend\View\Renderer\TreeRendererInterface;

abstract class AbstractRendererOptions extends AbstractOptions implements TreeRendererInterface
{
    /**
     * @var bool Whether or not to render trees of view models
     */
    protected $renderTrees = true;

    /**
     * Set flag indicating whether or not we should render trees of view models
     *
     * If set to true, the View instance will not attempt to render children
     * separately, but instead pass the root view model directly to the PhpRenderer.
     * It is then up to the developer to render the children from within the
     * view script.
     *
     * @param  bool $renderTrees
     * @return AbstractRendererOptions
     */
    public function setCanRenderTrees($renderTrees)
    {
        $this->renderTrees = (bool) $renderTrees;

        return $this;
    }

    /**
     * Can we render trees, or are we configured to do so?
     *
     * @return bool
     */
    public function canRenderTrees()
    {
        return $this->renderTrees;
    }
}
