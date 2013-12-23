<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Render;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Renderer\Renderer;

class Listener
    implements ListenerInterface,
               FactoryInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $eventName = self::EVENT_RENDER;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = self::WILDCARD;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $eventPriority = self::DEFAULT_PRIORITY;

    /**
     * Placeholders that may hold content
     *
     * @var array
     */
    protected $contentPlaceholders = array('article', 'content');

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        $this->setRenderer($sm->getViewRenderer());
        return $this;

    }

    /**
     * Set list of possible content placeholders
     *
     * @param  array $contentPlaceholders
     * @return Listener
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
     * @param Renderer $renderer
     * @return $this
     */
    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Select the PhpRenderer; typically, this will be registered last or at
     * low priority.
     *
     * @param  Event $event
     * @return Renderer
     */
    public function selectRenderer(Event $event)
    {
        $event->setRenderer($this->renderer);
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param Event $event
     * @return void
     */
    public function injectResponse(Event $event)
    {
        $renderer = $event->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }

        $result   = $event->getResult();
        $response = $event->getResponse();

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
        switch($event->getEventName())
        {
            case self::EVENT_RENDER:
                $this->selectRenderer($event);
                break;
            case self::EVENT_RESPONSE:
                $this->injectResponse($event);
                break;
        }
    }
}
