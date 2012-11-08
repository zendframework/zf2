<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace Zend\Test\Gateway;

use Zend\Dom;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Test\Gateway\Mvc\View\CaptureResponseListener;
use Zend\Uri\Http as HttpUri;

/**
 * @category   Zend
 * @package    Zend_Test
 */
class SharedService
{
    /**
     * @var Zend\Mvc\ApplicationInterface
     */
    protected $application;

    /**
     * @var array
     */
    protected $applicationConfig;

    /**
     * Flag to use console router or not
     * @var boolean
     */
    protected $useConsoleRequest = false;

    public function getUseConsoleRequest()
    {
        return $this->useConsoleRequest;
    }
    
    /**
     * Set the usage of the console router or not
     * @param boolean $boolean
     */
    public function setUseConsoleRequest($boolean)
    {
        $this->useConsoleRequest = (boolean)$boolean;
        return $this;
    }

    /**
     * Set the application config
     * @param array $applicationConfig
     */
    public function setApplicationConfig($applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
        return $this;
    }

    /**
     * Get the application object
     * @return Zend\Mvc\ApplicationInterface
     */
    public function getApplication()
    {
        if(null === $this->application) {
            $appConfig = $this->applicationConfig;
            if(!$this->useConsoleRequest) {
                $consoleServiceConfig = array(
                    'service_manager' => array(
                        'factories' => array(
                            'ServiceListener' => 'Zend\Test\Gateway\Mvc\Service\ServiceListenerFactory',
                        ),
                    ),
                );
                $appConfig = array_replace_recursive($appConfig, $consoleServiceConfig);
            }
            $this->application = Application::init($appConfig);
            $events = $this->application->getEventManager();
            $events->attach(new CaptureResponseListener);
        }
        return $this->application;
    }

    /**
     * Get the service manager of the application object
     * @return Zend\ServiceManager\ServiceManager
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
     * @return Zend\Stdlib\ResponseInterface
     */
    public function getResponse()
    {
        return $this->getApplication()->getResponse();
    }

    /**
     * Set the request URL
     *
     * @param string $url
     */
    public function url($url)
    {
        $request = $this->getRequest();
        if($this->useConsoleRequest) {
            $params = preg_split('#\s+#', $url);
            $request->params()->exchangeArray($params);
        } else {
            $uri = new HttpUri($url);
            $request->setUri($uri);
        }
    }

    /**
     * Dispatch the MVC with an URL
     * Accept a HTTP (simulate a customer action) or console route.
     *
     * The URL provided set the request URI in the request object.
     *
     * @param string $url
     */
    public function dispatch($url)
    {
        $this->url($url);
        $this->getApplication()->run();
    }

    /**
     * Trigger an application event
     *
     * @param string $eventName
     * @return Zend\EventManager\ResponseCollection
     */
    public function triggerApplicationEvent($eventName)
    {
        $events = $this->getApplication()->getEventManager();
        $event = $this->getApplication()->getMvcEvent();

        if($eventName == MvcEvent::EVENT_ROUTE || $eventName == MvcEvent::EVENT_DISPATCH) {
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
        return $events->trigger($eventName, $event);
    }
    
    public function getResponseStatusCode()
    {
        $response = $this->getResponse();
        if($this->useConsoleRequest) {
            $match = $response->getErrorLevel();
            if(null === $match) {
                $match = 0;
            }
            return $match;
        }
        return $response->getStatusCode();
    }
    
    public function getControllerFullClassName()
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $controllerIdentifier = $routeMatch->getParam('controller');
        $controllerManager = $this->getApplicationServiceLocator()->get('ControllerLoader');
        $controllerClass = $controllerManager->get($controllerIdentifier);
        return get_class($controllerClass);
    }
    
    public function query($path)
    {
        $response = $this->getResponse();
        $dom = new Dom\Query($response->getContent());
        $result = $dom->execute($path);
        return count($result);
    }
}
