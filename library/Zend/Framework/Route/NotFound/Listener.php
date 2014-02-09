<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\NotFound;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Dispatch\Error\EventInterface as DispatchError;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\View\Model\ViewModel;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventListener;

    /**
     * Create and return a 404 view model
     *
     * @param  EventInterface $event
     * @param mixed $result
     * @return void
     */
    public function __invoke(EventInterface $event, $result)
    {
        if (DispatchError::EVENT == $event->name()) {
            $this->detectNotFoundError($event);
        }

        $vars = $result;
        if ($vars instanceof Response) {
            // Already have a response as the result
            return;
        }

        $response = $event->response();
        if ($response->getStatusCode() != 404) {
            // Only handle 404 responses
            return;
        }

        if (!$response instanceof ViewModel) {
            $model = new ViewModel;
            if (is_string($response)) {
                $model->setVariable('message', $response);
            } else {
                $model->setVariable('message', 'Page not found.');
            }
        } else {
            $model = $response;
            if ($model->getVariable('message') === null) {
                $model->setVariable('message', 'Page not found.');
            }
        }

        $model->setTemplate($this->notFoundTemplate());

        // If displaying reasons, inject the reason
        $this->injectNotFoundReason($model);

        // If displaying exceptions, inject
        $this->injectException($model, $event);

        // Inject controller if we're displaying either the reason or the exception
        $this->injectController($model, $event);

        $event->setResponse($model);
    }
}
