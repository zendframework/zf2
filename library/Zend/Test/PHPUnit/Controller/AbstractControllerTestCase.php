<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Test\PHPUnit\Controller;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;
use Zend\EventManager\StaticEventManager;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\SendResponseListener;
use Zend\Stdlib\Exception\LogicException;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ResponseInterface;
use Zend\Uri\Http as HttpUri;
use Zend\View\Helper\Placeholder;

abstract class AbstractControllerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Mvc\ApplicationInterface
     */
    private $application;

    /**
     * @var array
     */
    private $applicationConfig;

    /**
     * Flag to use console router or not
     * @var boolean
     */
    protected $useConsoleRequest = false;

    /**
     * Trace error when exception is throwed in application
     * @var boolean
     */
    protected $traceError = false;

    /**
     * Reset the application for isolation
     */
    public function setUp()
    {
        $this->reset();
    }

    /**
     * Get the trace error flag
     * @return boolean
     */
    public function getTraceError()
    {
        return $this->traceError;
    }

    /**
     * Set the trace error flag
     * @param  boolean $traceError
     * @return AbstractControllerTestCase
     */
    public function setTraceError($traceError)
    {
        $this->traceError = $traceError;
        return $this;
    }

    /**
     * Get the usage of the console router or not
     * @return boolean $boolean
     */
    public function getUseConsoleRequest()
    {
        return $this->useConsoleRequest;
    }

    /**
     * Set the usage of the console router or not
     * @param  boolean $boolean
     * @return AbstractControllerTestCase
     */
    public function setUseConsoleRequest($boolean)
    {
        $this->useConsoleRequest = (boolean) $boolean;
        return $this;
    }

    /**
     * Get the application config
     * @return array the application config
     */
    public function getApplicationConfig()
    {
        return $this->applicationConfig;
    }

    /**
     * Set the application config
     * @param  array $applicationConfig
     * @throws LogicException
     */
    public function setApplicationConfig($applicationConfig)
    {
        if (null !== $this->application && null !== $this->applicationConfig) {
            throw new LogicException(
                'Application config can not be set, the application is already built'
            );
        }

        // do not cache module config on testing environment
        if (isset($applicationConfig['module_listener_options']['config_cache_enabled'])) {
            $applicationConfig['module_listener_options']['config_cache_enabled'] = false;
        }
        $this->applicationConfig = $applicationConfig;
        return $this;
    }

    /**
     * Get the application object
     * @return \Zend\Mvc\ApplicationInterface
     */
    public function getApplication()
    {
        if ($this->application) {
            return $this->application;
        }
        $appConfig = $this->applicationConfig;
        if (!$this->useConsoleRequest) {
            $consoleServiceConfig = array(
                'service_manager' => array(
                    'factories' => array(
                        'ServiceListener' => 'Zend\Test\PHPUnit\Mvc\Service\ServiceListenerFactory',
                    ),
                ),
            );
            $appConfig = array_replace_recursive($appConfig, $consoleServiceConfig);
        }
        $this->application = Application::init($appConfig);

        $events = $this->application->getEventManager();
        foreach($events->getListeners(MvcEvent::EVENT_FINISH) as $listener) {
            $callback = $listener->getCallback();
            if (is_array($callback) && $callback[0] instanceof SendResponseListener) {
                $events->detach($listener);
            }
        }
        return $this->application;
    }

    /**
     * Get the service manager of the application object
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getApplicationServiceLocator()
    {
        return $this->getApplication()->getServiceManager();
    }

    /**
     * Get the application request object
     * @return \Zend\Stdlib\RequestInterface
     */
    public function getRequest()
    {
        return $this->getApplication()->getRequest();
    }

    /**
     * Get the application response object
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->getApplication()->getResponse();
    }

    /**
     * Set the request URL
     *
     * @param  string $url
     * @return AbstractControllerTestCase
     */
    public function url($url, $method = HttpRequest::METHOD_GET, $params = array())
    {
        $request = $this->getRequest();
        if ($this->useConsoleRequest) {
            $params = preg_split('#\s+#', $url);
            $request->params()->exchangeArray($params);
            return $this;
        }

        $query       = $request->getQuery()->toArray();
        $post        = $request->getPost()->toArray();
        $uri         = new HttpUri($url);
        $queryString = $uri->getQuery();

        if ($queryString) {
            parse_str($queryString, $query);
        }

        if ($method == HttpRequest::METHOD_POST) {
            $post = $params;
        }

        if ($method == HttpRequest::METHOD_GET) {
            $query = array_merge($query, $params);
        }

        $request->setMethod($method);
        $request->setQuery(new Parameters($query));
        $request->setPost(new Parameters($post));
        $request->setUri($uri);

        return $this;
    }

    /**
     * Dispatch the MVC with an URL
     * Accept a HTTP (simulate a customer action) or console route.
     *
     * The URL provided set the request URI in the request object.
     *
     * @param  string $url
     * @throws \Exception
     */
    public function dispatch($url, $method = HttpRequest::METHOD_GET, $params = array())
    {
        $this->url($url, $method, $params);
        $this->getApplication()->run();

        if (true !== $this->traceError) {
            return;
        }

        $exception = $this->getApplication()->getMvcEvent()->getParam('exception');
        if ($exception instanceof \Exception) {
            throw $exception;
        }
    }

    /**
     * Reset the request
     *
     * @return AbstractControllerTestCase
     */
    public function reset()
    {
        // force to re-create all components
        $this->application = null;

        // reset server datas
        $_SESSION = array();
        $_GET     = array();
        $_POST    = array();
        $_COOKIE  = array();

        // reset singleton
        StaticEventManager::resetInstance();
        Placeholder\Registry::unsetRegistry();

        return $this;
    }

    /**
     * Trigger an application event
     *
     * @param  string $eventName
     * @return \Zend\EventManager\ResponseCollection
     */
    public function triggerApplicationEvent($eventName)
    {
        $events = $this->getApplication()->getEventManager();
        $event  = $this->getApplication()->getMvcEvent();

        if ($eventName != MvcEvent::EVENT_ROUTE && $eventName != MvcEvent::EVENT_DISPATCH) {
            return $events->trigger($eventName, $event);
        }

        $shortCircuit = function ($r) use ($event) {
            if ($r instanceof ResponseInterface) {
                return true;
            }

            if ($event->getError()) {
                return true;
            }

            return false;
        };

        return $events->trigger($eventName, $event, $shortCircuit);
    }

    /**
     * Assert modules were loaded with the module manager
     *
     * @param array $modules
     */
    public function assertModulesLoaded(array $modules)
    {
        $moduleManager = $this->getApplicationServiceLocator()->get('ModuleManager');
        $modulesLoaded = $moduleManager->getModules();
        $list          = array_diff($modules, $modulesLoaded);
        if ($list) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Several modules are not loaded "%s"', implode(', ', $list)
            ));
        }
        $this->assertEquals(count($list), 0);
    }

    /**
     * Assert modules were not loaded with the module manager
     *
     * @param  array $modules
     */
    public function assertNotModulesLoaded(array $modules)
    {
        $moduleManager = $this->getApplicationServiceLocator()->get('ModuleManager');
        $modulesLoaded = $moduleManager->getModules();
        $list          = array_intersect($modules, $modulesLoaded);
        if ($list) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Several modules WAS not loaded "%s"', implode(', ', $list)
            ));
        }
        $this->assertEquals(count($list), 0);
    }

    /**
     * Retrieve the response status code
     *
     * @return int
     */
    protected function getResponseStatusCode()
    {
        $response = $this->getResponse();
        if (!$this->useConsoleRequest) {
            return $response->getStatusCode();
        }

        $match = $response->getErrorLevel();
        if (null === $match) {
            $match = 0;
        }
        return $match;
    }

    /**
     * Assert response status code
     *
     * @param  int $code
     */
    public function assertResponseStatusCode($code)
    {
        if ($this->useConsoleRequest) {
            if (!in_array($code, array(0, 1))) {
                throw new PHPUnit_Framework_ExpectationFailedException(
                    'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if ($code != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response code "%s", actual status code is "%s"',
                $code, $match
            ));
        }
        $this->assertEquals($code, $match);
    }

    /**
     * Assert not response status code
     *
     * @param  int $code
     */
    public function assertNotResponseStatusCode($code)
    {
        if ($this->useConsoleRequest) {
            if (!in_array($code, array(0, 1))) {
                throw new PHPUnit_Framework_ExpectationFailedException(
                    'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if ($code == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response code was NOT "%s"',
                $code
            ));
        }
        $this->assertNotEquals($code, $match);
    }

    /**
     * Assert the application exception and message
     *
     * @param $type application exception type
     * @param $message application exception message
     */
    public function assertApplicationException($type, $message = null)
    {
        $exception = $this->getApplication()->getMvcEvent()->getParam('exception');
        if (!$exception) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting application exception, exception not exist'
            );
        }
        if (true === $this->traceError) {
            // set exception as null because we know and have assert the exception
            $this->getApplication()->getMvcEvent()->setParam('exception', null);
        }
        $this->setExpectedException($type, $message);
        throw $exception;
    }

    /**
     * Get the full current controller class name
     *
     * @return string
     */
    protected function getControllerFullClassName()
    {
        $routeMatch           = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $controllerIdentifier = $routeMatch->getParam('controller');
        $controllerManager    = $this->getApplicationServiceLocator()->get('ControllerLoader');
        $controllerClass      = $controllerManager->get($controllerIdentifier);
        return get_class($controllerClass);
    }

    /**
     * Assert that the application route match used the given module
     *
     * @param  string $module
     */
    public function assertModuleName($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match           = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $match           = strtolower($match);
        $module          = strtolower($module);
        if ($module != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting module name "%s", actual module name is "%s"',
                $module, $match
            ));
        }
        $this->assertEquals($module, $match);
    }

    /**
     * Assert that the application route match used NOT the given module
     *
     * @param  string $module
     */
    public function assertNotModuleName($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match           = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $match           = strtolower($match);
        $module          = strtolower($module);
        if ($module == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting module was NOT "%s"',
                $module
            ));
        }
        $this->assertNotEquals($module, $match);
    }

    /**
     * Assert that the application route match used the given controller class
     *
     * @param  string $controller
     */
    public function assertControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match           = substr($controllerClass, strrpos($controllerClass, '\\')+1);
        $match           = strtolower($match);
        $controller      = strtolower($controller);
        if ($controller != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller class "%s", actual controller class is "%s"',
                $controller, $match
            ));
        }
        $this->assertEquals($controller, $match);
    }

    /**
     * Assert that the application route match used NOT the given controller class
     *
     * @param  string $controller
     */
    public function assertNotControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match           = substr($controllerClass, strrpos($controllerClass, '\\')+1);
        $match           = strtolower($match);
        $controller      = strtolower($controller);
        if ($controller == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller class was NOT "%s"',
                $controller
            ));
        }
        $this->assertNotEquals($controller, $match);
    }

    /**
     * Assert that the application route match used the given controller name
     *
     * @param  string $controller
     */
    public function assertControllerName($controller)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match      = $routeMatch->getParam('controller');
        $match      = strtolower($match);
        $controller = strtolower($controller);
        if ($controller != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller name "%s", actual controller name is "%s"',
                $controller, $match
            ));
        }
        $this->assertEquals($controller, $match);
    }

    /**
     * Assert that the application route match used NOT the given controller name
     *
     * @param  string $controller
     */
    public function assertNotControllerName($controller)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match      = $routeMatch->getParam('controller');
        $match      = strtolower($match);
        $controller = strtolower($controller);
        if ($controller == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller name was NOT "%s"',
                $controller
            ));
        }
        $this->assertNotEquals($controller, $match);
    }

    /**
     * Assert that the application route match used the given action
     *
     * @param  string $action
     */
    public function assertActionName($action)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match      = $routeMatch->getParam('action');
        $match      = strtolower($match);
        $action     = strtolower($action);
        if ($action != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting action name "%s", actual action name is "%s"',
                $action, $match
            ));
        }
        $this->assertEquals($action, $match);
    }

    /**
     * Assert that the application route match used NOT the given action
     *
     * @param  string $action
     */
    public function assertNotActionName($action)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match      = $routeMatch->getParam('action');
        $match      = strtolower($match);
        $action     = strtolower($action);
        if ($action == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting action name was NOT "%s"',
                $action
            ));
        }
        $this->assertNotEquals($action, $match);
    }

    /**
     * Assert that the application route match used the given route name
     *
     * @param  string $route
     */
    public function assertMatchedRouteName($route)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match      = $routeMatch->getMatchedRouteName();
        $match      = strtolower($match);
        $route      = strtolower($route);
        if ($route != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting matched route name was "%s", actual matched route name is "%s"',
                $route, $match
            ));
        }
        $this->assertEquals($route, $match);
    }

    /**
     * Assert that the application route match used NOT the given route name
     *
     * @param  string $route
     */
    public function assertNotMatchedRouteName($route)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match      = $routeMatch->getMatchedRouteName();
        $match      = strtolower($match);
        $route      = strtolower($route);
        if ($route == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting route matched was NOT "%s"', $route
            ));
        }
        $this->assertNotEquals($route, $match);
    }
}
