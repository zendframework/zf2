<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model\Render;

use RuntimeException;
use Zend\Framework\View\Manager\ServiceTrait as ViewManager;
use Zend\View\Renderer\TreeRendererInterface;
use Zend\View\Exception\DomainException;
use Zend\View\Model\ModelInterface as ViewModel;

trait ServiceTrait
{
    /**
     *
     */
    use ViewManager;

    /**
     * @param ViewModel $model
     * @return mixed
     * @throws RuntimeException
     */
    public function render(ViewModel $model)
    {
        $renderer = $this->renderer($model);

        // If we have children, render them first, but only if:
        // a) the renderer does not implement TreeRendererInterface, or
        // b) it does, but canRenderTrees() returns false
        if ($model->hasChildren() && (!$renderer instanceof TreeRendererInterface || !$renderer->canRenderTrees())) {
            $this->renderChildren($model);
        }

        return $renderer->render($model);
    }

    /**
     * Loop through children, rendering each
     *
     * @param  ViewModel $model
     * @throws DomainException
     * @return void
     */
    protected function renderChildren(ViewModel $model)
    {
        foreach ($model as $child) {
            if ($child->terminate()) {
                throw new DomainException('Inconsistent state; child view model is marked as terminal');
            }

            $result  = $this->render($child);

            $capture = $child->captureTo();

            if (empty($capture)) {
                continue;
            }

            if ($child->isAppend()) {
                $result = $model->{$capture} . $result;
            }

            $model->setVariable($capture, $result);
        }
    }
}
