<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\View\Renderer\RendererInterface as Renderer;

class Event
    implements EventInterface, EventListenerInterface
{
    /**
     *
     */
    use EventTrait {
        EventTrait::__construct as event;
    }

    /**
     * @param string $name
     * @param string $target
     */
    public function __construct($name = self::EVENT_RENDER, $target = null)
    {
        $this->event($name, $target);
    }

    /**
     * @param Listener $listener
     * @return bool
     */
    public function __invoke(Listener $listener)
    {
        $listener->__invoke($this);

        if ($this->getViewRenderer() instanceof Renderer) {
            $this->stopped = true;
        }

        return $this->stopped;
    }
}
