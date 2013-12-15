<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Strategy;

use Zend\Framework\EventManager\AbstractListenerAggregate;
use Zend\Framework\EventManager\ManagerInterface as EventManager;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\ViewEvent;

use Zend\Framework\ServiceManager\FactoryInterface;

class PhpRendererStrategy
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var array
     */
    protected $name = [
        ViewEvent::EVENT_RENDERER,
        ViewEvent::EVENT_RESPONSE
    ];

    /**
     * Placeholders that may hold content
     *
     * @var array
     */
    protected $contentPlaceholders = array('article', 'content');

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * @param ServiceManager $sm
     * @return mixed|PhpRendererStrategy
     */
    public function createService(ServiceManager $sm)
    {
        $listener = new self();

        $listener->setRenderer($sm->getViewRenderer());

        return $listener;

    }

    /**
     * Retrieve the composed renderer
     *
     * @return PhpRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param PhpRenderer $renderer
     * @return $this
     */
    public function setRenderer(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Set list of possible content placeholders
     *
     * @param  array $contentPlaceholders
     * @return PhpRendererStrategy
     */
    public function setContentPlaceholders(array $contentPlaceholders)
    {
        $this->contentPlaceholders = $contentPlaceholders;
        return $this;
    }

    /**
     * Get list of possible content placeholders
     *
     * @return array
     */
    public function getContentPlaceholders()
    {
        return $this->contentPlaceholders;
    }

    /**
     * Select the PhpRenderer; typically, this will be registered last or at
     * low priority.
     *
     * @param  Event $e
     * @return PhpRenderer
     */
    public function selectRenderer(Event $e)
    {
        return $this->renderer;
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param Event $e
     * @return void
     */
    public function injectResponse(Event $e)
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }

        $result   = $e->getResult();
        $response = $e->getResponse();

        // Set content
        // If content is empty, check common placeholders to determine if they are
        // populated, and set the content from them.
        if (empty($result)) {
            $placeholders = $renderer->plugin('placeholder');
            foreach ($this->contentPlaceholders as $placeholder) {
                if ($placeholders->containerExists($placeholder)) {
                    $result = (string) $placeholders->getContainer($placeholder);
                    break;
                }
            }
        }
        $response->setContent($result);
    }

    /**
     * @param Event $event
     * @return mixed|void
     */
    public function __invoke(Event $event)
    {
        switch($event->getName())
        {
            case ViewEvent::EVENT_RENDERER:
                return $this->selectRenderer($event);
                break;
            case ViewEvent::EVENT_RESPONSE:
                $this->injectResponse($event);
                break;
        }
    }
}
