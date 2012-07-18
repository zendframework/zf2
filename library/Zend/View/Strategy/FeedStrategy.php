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
use Zend\Feed\Writer\Feed;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\View\Model;
use Zend\View\Renderer\FeedRenderer;
use Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Strategy
 */
class FeedStrategy implements StrategyInterface, ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var FeedRenderer
     */
    protected $renderer;

    /**
     * @var double
     */
    protected $matchPriority = 0;

    /**
     * Constructor
     *
     * @param  FeedRenderer $renderer
     * @return void
     */
    public function __construct(FeedRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Retrieve the composed renderer
     *
     * @return FeedRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
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
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, array($this, 'resolveStrategyPriority'), $priority);
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
     * The match priority, normally a double between 0 and 1
     *
     * @return double
     */
    public function getMatchPriority()
    {
        return $this->matchPriority;
    }

    /**
     * Detect if we should use the FeedRenderer based on model type and/or
     * Accept header
     *
     * @param  ViewEvent $e
     * @return null|FeedRenderer
     */
    public function resolveStrategyPriority(ViewEvent $e)
    {
        $model = $e->getModel();

        if ($model instanceof Model\FeedModel) {
            // FeedModel found
            $this->matchPriority = 1;
            return $this;
        }

        $request = $e->getRequest();
        if (!$request instanceof HttpRequest) {
            // Not an HTTP request; cannot autodetermine
            return;
        }

        $headers = $request->getHeaders();
        if (!$headers->has('accept')) {
            return;
        }

        $accept  = $headers->get('accept');
        if (($match = $accept->match('application/rss+xml, application/atom+xml')) == false) {
            return;
        }
        $this->matchPriority = $match->getPriority();

        if ($match->getTypeString() == 'application/rss+xml') {
            $this->renderer->setFeedType('rss');
            return $this;
        }

        if ($match->getTypeString() == 'application/atom+xml') {
            $this->renderer->setFeedType('atom');
            return $this;
        }

    }

    /**
     * Inject the response with the feed payload and appropriate Content-Type header
     *
     * @param  ViewEvent $e
     * @return void
     */
    public function injectResponse(ViewEvent $e)
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            // Discovered renderer is not ours; do nothing
            return;
        }

        $result   = $e->getResult();
        if (!is_string($result) && !$result instanceof Feed) {
            // We don't have a string, and thus, no feed
            return;
        }

        // If the result is a feed, export it
        if ($result instanceof Feed) {
            $result = $result->export($renderer->getFeedType());
        }

        // Get the content-type header based on feed type
        $feedType = $renderer->getFeedType();
        $feedType = ('rss' == $feedType)
                  ? 'application/rss+xml'
                  : 'application/atom+xml';

        $model   = $e->getModel();
        $charset = '';

        if ($model instanceof Model\FeedModel) {

            $feed = $model->getFeed();

            $charset = '; charset=' . $feed->getEncoding() . ';';
        }

        // Populate response
        $response = $e->getResponse();
        $response->setContent($result);
        $headers = $response->getHeaders();
        $headers->addHeaderLine('content-type', $feedType . $charset);
    }
}
