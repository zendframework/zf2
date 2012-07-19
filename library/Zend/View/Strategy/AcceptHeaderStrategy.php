<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Strategy;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\View\Model;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Strategy\AcceptHeaderStrategy\AcceptHeaderStrategyInterface;
use Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Strategy
 */
class AcceptHeaderStrategy implements StrategyAggregateInterface, ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var AcceptHeaderStrategyInterface[]
     */
    protected $acceptHeaderStrategies = array();

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Add a strategy to the array of Accept Header strategies
     *
     * @param mixed $strategy
     */
    public function addStrategy($strategy)
    {
        $this->acceptHeaderStrategies[] = $strategy;
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @param  int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $priority);
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $priority);
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Detect if we should use one of the registered AcceptHeader strategies
     *
     * @param  ViewEvent $e
     * @return null|RendererInterface
     */
    public function selectRenderer(ViewEvent $e)
    {
        $request = $e->getRequest();
        if (!$request instanceof HttpRequest) {
            // Not an HTTP request; cannot autodetermine
            return;
        }

        $headers = $request->getHeaders();
        if (!$headers->has('accept')) {
            return;
        }

        $fieldValueParts = array(); // Iterator that allows for equally named keys
        foreach($this->acceptHeaderStrategies as $key => $acceptStrategy) {
            foreach($acceptStrategy->getFieldValueParts() as $fieldValuePart) {
                $fieldValuePart->setMatchId($key);
                $fieldValueParts[] = $fieldValuePart;
            }
        }

        $accept = $headers->get('Accept');
        if (false == ($match = $accept->match($fieldValueParts))) {
            return;
        }

        $acceptStrategy = $this->acceptHeaderStrategies[$match->getMatchId()];

        //need to send the matched content type to the strategy in case it needs to setup the renderer
        $this->renderer = $acceptStrategy->getRenderer($e, $match);
        return $this;
    }

    /**
     * Inject the response with the feed payload and appropriate Content-Type header
     *
     * @param  ViewEvent $e
     * @return void
     */
    public function injectResponse(ViewEvent $e)
    {
        if (is_null($this->renderer)) {
            return;
        }
        // we need the matched strategy to do it's inject response routine
        die(__FUNCTION__);
    }
}
