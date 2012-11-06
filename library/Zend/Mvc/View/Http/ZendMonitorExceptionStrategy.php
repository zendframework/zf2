<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\View\Http;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 */
class ZendMonitorExceptionStrategy implements ListenerAggregateInterface
{
     /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'createCustomEvent'));
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
     * Create a custom event in Zend Server
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function createCustomEvent(MvcEvent $e)
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
                if (!empty($e)) {
                    $exception = $e->getParam('exception'); /* @var $exception \Exception */
                    $message = 'An error occurred during execution: ' . $exception->getMessage();
                    if ($this->isCustomEventByRuleIsEnabled()) {
                        zend_monitor_custom_event_ex('Zend Framework Exception', $message, 'Zend Framework Exception Rule');
                    } elseif ($this->isCustomEventIsEnabled) {
                        zend_monitor_custom_event('Zend Framework Exception', $message);
                    }
                }
        }
    }
    
    /**
     * Is Zend Server Monitor enabled?
     * @return boolean
     */
    protected function isCustomEventByRuleIsEnabled()
    {
        return function_exists('zend_monitor_custom_event_ex');
    }
    
    /**
     * Is Zend Server Monitor enabled?
     * @return boolean
     */
    protected function isCustomEventIsEnabled() 
    {
        return function_exists('zend_monitor_custom_event');
    }
}
