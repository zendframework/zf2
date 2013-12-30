<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Framework\View\Renderer\Event as ViewRendererEvent;
use Zend\Framework\View\Response\Event as ViewResponseEvent;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Renderer\TreeRendererInterface;
use Zend\View\Exception\DomainException;
use Zend\View\Exception\RuntimeException;
use Zend\Framework\View\Model\ViewModel;


trait ListenerTrait
{
    /**
     *
     */
    use ListenerService;

    /**
     * @param ViewModel $model
     * @param EventInterface $event
     * @return mixed
     * @throws RuntimeException
     */
    public function render(ViewModel $model, EventInterface $event)
    {
        $em = $event->eventManager();
        $sm = $event->serviceManager();

        $rendererEvent = new ViewRendererEvent;

        $rendererEvent->setServiceManager($sm)
            //->setName(ViewRenderer::EVENT_VIEW_RENDERER)
            ->setTarget($event->target())
            ->setViewModel($model);


        $em->__invoke($rendererEvent);

        $renderer = $rendererEvent->viewRenderer();

        if (!$renderer instanceof Renderer) {
            throw new RuntimeException(sprintf(
                '%s: no renderer selected!',
                __METHOD__
            ));
        }

        // If EVENT_VIEW_RENDERER changed the model, make sure
        // we use this new model instead of the current $model
        $model   = $rendererEvent->viewModel();

        // If we have children, render them first, but only if:
        // a) the renderer does not implement TreeRendererInterface, or
        // b) it does, but canRenderTrees() returns false
        if ($model->hasChildren()
            && (!$renderer instanceof TreeRendererInterface
                || !$renderer->canRenderTrees())
        ) {
            $this->renderChildren($model, $event);
        }

        // Reset the model, in case it has changed, and set the renderer
        $event->setViewModel($model);
        $event->setViewRenderer($renderer);

        $rendered = $renderer->render($model);

        // If this is a child model, return the rendered content; do not
        // invoke the response strategy.
        $options = $model->getOptions();
        if (array_key_exists('has_parent', $options) && $options['has_parent']) {
            return $rendered;
        }

        $responseEvent = new ViewResponseEvent;
        $responseEvent->setServiceManager($sm)
            //->setName(ViewResponse::EVENT_VIEW_RESPONSE)
            ->setTarget($event->target())
            ->setResult($rendered);

        $em->__invoke($responseEvent);
    }

    /**
     * Loop through children, rendering each
     *
     * @param  ViewModel $model
     * @param EventInterface $event
     * @throws DomainException
     * @return void
     */
    protected function renderChildren(ViewModel $model, EventInterface $event)
    {
        foreach ($model as $child) {
            if ($child->terminate()) {
                throw new DomainException('Inconsistent state; child view model is marked as terminal');
            }
            $child->setOption('has_parent', true);
            $result  = $this->render($child, $event);
            $child->setOption('has_parent', null);
            $capture = $child->captureTo();
            if (!empty($capture)) {
                if ($child->isAppend()) {
                    $oldResult=$model->{$capture};
                    $model->setVariable($capture, $oldResult . $result);
                } else {
                    $model->setVariable($capture, $result);
                }
            }
        }
    }
}
