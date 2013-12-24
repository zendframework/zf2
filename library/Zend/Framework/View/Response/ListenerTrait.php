<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Response;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Framework\ApplicationServiceTrait as Services;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService, Services;

    /**
     * Placeholders that may hold content
     *
     * @var array
     */
    protected $contentPlaceholders = array('article', 'content');

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
        $renderer = $event->getViewRenderer();
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
}
