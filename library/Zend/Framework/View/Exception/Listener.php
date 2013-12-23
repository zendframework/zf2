<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Exception;

use Zend\EventManager\CallbackListener;
use Zend\Framework\Application;
use Zend\Framework\EventManager\ListenerTrait;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Http\Response as HttpResponse;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;

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
    protected $eventName = self::EVENT_EXCEPTION;

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
     * Display exceptions?
     * @var bool
     */
    protected $displayExceptions = false;

    /**
     * Name of exception template
     * @var string
     */
    protected $exceptionTemplate = 'error';

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function createService(ServiceManager $sm)
    {
        $vm = $sm->getViewManager();

        $this->setDisplayExceptions($vm->displayExceptions());

        $this->setExceptionTemplate($vm->exceptionTemplate());

        return $this;
    }

    /**
     * Flag: display exceptions in error pages?
     *
     * @param  bool $displayExceptions
     * @return $this
     */
    public function setDisplayExceptions($displayExceptions)
    {
        $this->displayExceptions = (bool) $displayExceptions;
        return $this;
    }

    /**
     * Should we display exceptions in error pages?
     *
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->displayExceptions;
    }

    /**
     * Set the exception template
     *
     * @param  string $exceptionTemplate
     * @return $this
     */
    public function setExceptionTemplate($exceptionTemplate)
    {
        $this->exceptionTemplate = (string) $exceptionTemplate;
        return $this;
    }

    /**
     * Retrieve the exception template
     *
     * @return string
     */
    public function getExceptionTemplate()
    {
        return $this->exceptionTemplate;
    }

    /**
     * Create an exception view model, and set the HTTP status code
     *
     * @todo   dispatch.error does not halt dispatch unless a response is
     *         returned. As such, we likely need to trigger rendering as a low
     *         priority dispatch.error event (or goto a render event) to ensure
     *         rendering occurs, and that munging of view models occurs when
     *         expected.
     * @param  Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        // Do nothing if no error in the event
        $error = $event->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;

            case Application::ERROR_EXCEPTION:
            default:
                $model = new ViewModel(array(
                    'message'            => 'An error occurred during execution; please try again later.',
                    'exception'          => $event->getParam('exception'),
                    'display_exceptions' => $this->displayExceptions(),
                ));
                $model->setTemplate($this->getExceptionTemplate());
                $event->setResult($model);

                $response = $event->getResponse();
                if (!$response) {
                    $response = new HttpResponse();
                    $response->setStatusCode(500);
                    $event->setResponse($response);
                } else {
                    $statusCode = $response->getStatusCode();
                    if ($statusCode === 200) {
                        $response->setStatusCode(500);
                    }
                }

                break;
        }
    }
}
