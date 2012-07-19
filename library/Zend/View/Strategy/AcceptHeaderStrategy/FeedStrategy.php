<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Strategy\AcceptHeaderStrategy;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Feed\Writer\Feed;
use Zend\Http\Header\Accept;
use Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart;
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
class FeedStrategy
{
    /**
     * @var FeedRenderer
     */
    protected $renderer;

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

    public function getFieldValueParts()
    {
        $acceptHeader = Accept::fromString('application/rss+xml,application/atom+xml');
        return $acceptHeader->getPrioritized();
    }

    public function getRenderer(ViewEvent $e, AcceptFieldValuePart $match)
    {
        if ($match->getTypeString() == 'application/rss+xml') {
            $this->renderer->setFeedType('rss');
        }

        if ($match->getTypeString() == 'application/atom+xml') {
            $this->renderer->setFeedType('atom');
        }

        return $this->renderer;
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
