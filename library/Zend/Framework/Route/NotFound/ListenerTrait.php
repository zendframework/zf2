<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\NotFound;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Framework\Route\EventInterface as RouteEvent;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\Framework\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Response as HttpResponse;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * Whether or not to display exceptions related to the 404 condition
     *
     * @var bool
     */
    protected $displayExceptions = false;

    /**
     * Whether or not to display the reason for a 404
     *
     * @var bool
     */
    protected $displayNotFoundReason = false;

    /**
     * Template to use to report page not found conditions
     *
     * @var string
     */
    protected $notFoundTemplate = '404';

    /**
     * The reason for a not-found condition
     *
     * @var false|string
     */
    protected $reason = false;

    public function createService(ServiceManager $sm)
    {
        $vm = $sm->viewManager();

        $this->setDisplayExceptions($vm->displayExceptions())
            ->setDisplayNotFoundReason($vm->displayNotFoundReason())
            ->setNotFoundTemplate($vm->notFoundTemplate());

        return $this;
    }

    /**
     * Set value indicating whether or not to display exceptions related to a not-found condition
     *
     * @param  bool $displayExceptions
     * @return self
     */
    public function setDisplayExceptions($displayExceptions)
    {
        $this->displayExceptions = (bool) $displayExceptions;
        return $this;
    }

    /**
     * Should we display exceptions related to a not-found condition?
     *
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->displayExceptions;
    }

    /**
     * Set value indicating whether or not to display the reason for a not-found condition
     *
     * @param  bool $displayNotFoundReason
     * @return self
     */
    public function setDisplayNotFoundReason($displayNotFoundReason)
    {
        $this->displayNotFoundReason = (bool) $displayNotFoundReason;
        return $this;
    }

    /**
     * Should we display the reason for a not-found condition?
     *
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->displayNotFoundReason;
    }

    /**
     * Get template for not found conditions
     *
     * @param  string $notFoundTemplate
     * @return self
     */
    public function setNotFoundTemplate($notFoundTemplate)
    {
        $this->notFoundTemplate = (string) $notFoundTemplate;
        return $this;
    }

    /**
     * Get template for not found conditions
     *
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->notFoundTemplate;
    }

    /**
     * Detect if an error is a 404 condition
     *
     * If a "controller not found" or "invalid controller" error type is
     * encountered, sets the response status code to 404.
     *
     * @param  Event $e
     * @return void
     */
    public function detectNotFoundError(Event $e)
    {
        $error = $e->error();
        if (empty($error)) {
            return;
        }

        switch ($error) {
            case RouteEvent::ERROR_CONTROLLER_NOT_FOUND:
            case RouteEvent::ERROR_CONTROLLER_INVALID:
            case RouteEvent::ERROR_ROUTER_NO_MATCH:
                $this->reason = $error;
                $response = $e->response();
                if (!$response) {
                    $response = new HttpResponse();
                    $e->setResponse($response);
                }
                $response->setStatusCode(404);
                break;
            default:
                return;
        }
    }

    /**
     * Inject the not-found reason into the model
     *
     * If $displayNotFoundReason is enabled, checks to see if $reason is set,
     * and, if so, injects it into the model. If not, it injects
     * RouteEvent::ERROR_CONTROLLER_CANNOT_DISPATCH.
     *
     * @param  ViewModel $model
     * @return void
     */
    protected function injectNotFoundReason(ViewModel $model)
    {
        if (!$this->displayNotFoundReason()) {
            return;
        }

        // no route match, controller not found, or controller invalid
        if ($this->reason) {
            $model->setVariable('reason', $this->reason);
            return;
        }

        // otherwise, must be a case of the controller not being able to
        // dispatch itself.
        $model->setVariable('reason', RouteEvent::ERROR_CONTROLLER_CANNOT_DISPATCH);
    }

    /**
     * Inject the exception message into the model
     *
     * If $displayExceptions is enabled, and an exception is found in the
     * event, inject it into the model.
     *
     * @param  ViewModel $model
     * @param  Event $e
     * @return void
     */
    protected function injectException($model, $e)
    {
        if (!$this->displayExceptions()) {
            return;
        }

        $model->setVariable('display_exceptions', true);

        if (!$e instanceof \Exception) {
            return;
        }

        $model->setVariable('exception', $e);
    }

    /**
     * Inject the controller and controller class into the model
     *
     * If either $displayExceptions or $displayNotFoundReason are enabled,
     * injects the controllerClass from the MvcEvent. It checks to see if a
     * controller is present in the MvcEvent, and, if not, grabs it from
     * the route match if present; if a controller is found, it injects it into
     * the model.
     *
     * @param  ViewModel $model
     * @param  Event $e
     * @return void
     */
    protected function injectController($model, $e)
    {
        if (!$this->displayExceptions() && !$this->displayNotFoundReason()) {
            return;
        }

        $controller = $e->controller();
        if (empty($controller)) {
            $routeMatch = $e->routeMatch();
            if (empty($routeMatch)) {
                return;
            }

            $controller = $routeMatch->getParam('controller', false);
            if (!$controller) {
                return;
            }
        }

        $controllerClass = $e->controllerClass();
        $model->setVariable('controller', $controller);
        $model->setVariable('controller_class', $controllerClass);
    }
}
