<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Exception;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Route\EventInterface as Route;
use Zend\Framework\View\Model\ViewModel;
use Zend\Http\Response as HttpResponse;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_EXCEPTION, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * Create an exception view model, and set the HTTP status code
     *
     * @todo   dispatch.error does not halt dispatch unless a response is
     *         returned. As such, we likely need to trigger rendering as a low
     *         priority dispatch.error event (or goto a render event) to ensure
     *         rendering occurs, and that munging of view models occurs when
     *         expected.
     * @param  EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        // Do nothing if no error in the event
        $error = $event->error();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $event->result();
        if ($result instanceof Response) {
            return;
        }

        switch ($error) {
            case Route::ERROR_CONTROLLER_NOT_FOUND:
            case Route::ERROR_CONTROLLER_INVALID:
            case Route::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;

            case Route::ERROR_EXCEPTION:
            default:
                $model = new ViewModel(array(
                    'message'            => 'An error occurred during execution; please try again later.',
                    'exception'          => $event->getParam('exception'),
                    'display_exceptions' => $this->displayExceptions(),
                ));
                $model->setTemplate($this->exceptionTemplate());
                $event->setResult($model);

                $response = $event->response();
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
