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
use Zend\Http\Header\Accept;
use Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\View\Model;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Strategy
 */
class JsonStrategy
{
    /**
     * @var JsonRenderer
     */
    protected $renderer;

    /**
     * Constructor
     *
     * @param  JsonRenderer $renderer
     * @return void
     */
    public function __construct(JsonRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFieldValueParts()
    {
        $acceptHeader = Accept::fromString('application/json,application/javascript');
        return $acceptHeader->getPrioritized();
    }

    public function getRenderer(ViewEvent $e, AcceptFieldValuePart $match)
    {
        if ('javascript' == $match->getFormat()) {
            // only check for callback for javascript format
            $request = $e->getRequest();
            if ($request instanceof HttpRequest) {
                // only can check for callback if HttpRequest
                if (false != ($callback = $request->getQuery()->get('callback'))) {
                    $this->renderer->setJsonpCallback($callback);
                }
            }
        }

        return $this->renderer;
    }

    /**
     * Inject the response with the JSON payload and appropriate Content-Type header
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
        if (!is_string($result)) {
            // We don't have a string, and thus, no JSON
            return;
        }

        // Populate response
        $response = $e->getResponse();
        $response->setContent($result);
        $headers = $response->getHeaders();
        if ($this->renderer->hasJsonpCallback()) {
            $headers->addHeaderLine('content-type', 'application/javascript');
        } else {
            $headers->addHeaderLine('content-type', 'application/json');
        }
    }
}
