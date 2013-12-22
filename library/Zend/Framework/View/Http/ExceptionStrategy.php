<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Http;

use Zend\EventManager\CallbackListener;
use Zend\Framework\EventManager\Listener as ParentListener;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ServiceRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Framework\Application;
use Zend\Framework\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class ExceptionStrategy
    extends ParentListener
    implements FactoryInterface
{
    /**
     * @var array
     */
    protected $eventName = [
        MvcEvent::EVENT_DISPATCH_ERROR,
        MvcEvent::EVENT_RENDER_ERROR
    ];

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
     * @return ExceptionStrategy
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->getViewManager()->getViewConfig();

        $this->setDisplayExceptions($config->get('display_exceptions'));

        $this->setExceptionTemplate($config->get('exception_template'));

        return $this;
    }

    /**
     * Flag: display exceptions in error pages?
     *
     * @param  bool $displayExceptions
     * @return ExceptionStrategy
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
     * @return ExceptionStrategy
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
     * @param  Event $e
     * @return void
     */
    public function __invoke(Event $e)
    {
        // Do nothing if no error in the event
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $e->getResult();
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
                    'exception'          => $e->getParam('exception'),
                    'display_exceptions' => $this->displayExceptions(),
                ));
                $model->setTemplate($this->getExceptionTemplate());
                $e->setResult($model);

                $response = $e->getResponse();
                if (!$response) {
                    $response = new HttpResponse();
                    $response->setStatusCode(500);
                    $e->setResponse($response);
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
