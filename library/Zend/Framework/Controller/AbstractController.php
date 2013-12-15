<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\EventManager;
use Zend\Framework\EventManager\EventManagerAwareInterface;
use Zend\Framework\EventManager\ManagerInterface as EventManagerInterface;
use Zend\Framework\EventManager\CallbackListener;
use Zend\Framework\EventManager\Listener as EventListener;

use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Http\Request as HttpRequest;

use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Framework\MvcEvent;

use Zend\Framework\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Framework\ServiceManager\ServiceLocatorInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\CreateServiceTrait as CreateService;

use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * Abstract controller
 *
 * Convenience methods for pre-built plugins (@see __call):
 *
 * @method \Zend\View\Model\ModelInterface acceptableViewModelSelector(array $matchAgainst = null, bool $returnDefault = true, \Zend\Http\Header\Accept\FieldValuePart\AbstractFieldValuePart $resultReference = null)
 * @method bool|array|\Zend\Http\Response fileprg(\Zend\Form\Form $form, $redirect = null, $redirectToUrl = false)
 * @method bool|array|\Zend\Http\Response filePostRedirectGet(\Zend\Form\Form $form, $redirect = null, $redirectToUrl = false)
 * @method \Zend\Mvc\Controller\Plugin\FlashMessenger flashMessenger()
 * @method \Zend\Mvc\Controller\Plugin\Forward forward()
 * @method mixed|null identity()
 * @method \Zend\Mvc\Controller\Plugin\Layout|\Zend\View\Model\ModelInterface layout(string $template = null)
 * @method \Zend\Mvc\Controller\Plugin\Params|mixed params(string $param = null, mixed $default = null)
 * @method \Zend\Http\Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 * @method \Zend\Http\Response|array postRedirectGet(string $redirect = null, bool $redirectToUrl = false)
 * @method \Zend\Mvc\Controller\Plugin\Redirect redirect()
 * @method \Zend\Mvc\Controller\Plugin\Url url()
 */
abstract class AbstractController extends EventListener
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_CONTROLLER_DISPATCH;

    /**
     * @var PluginManager
     */
    protected $plugins;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param ServiceManager $sm
     * @return mixed|static
     */
    public function createService(ServiceManager $sm)
    {
        $listener = new static();

        $listener->setPluginManager($sm->getControllerPluginManager());

        return $listener;
    }

    /**
     * Get plugin manager
     *
     * @return PluginManager
     */
    public function getPluginManager()
    {
        return $this->plugins;
    }

    /**
     * Set plugin manager
     *
     * @param  PluginManager $plugins
     * @return AbstractController
     */
    public function setPluginManager(PluginManager $plugins)
    {
        $this->plugins = $plugins;
        $this->plugins->setController($this);

        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name    Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array  $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }

        return $plugin;
    }

    /**
     * Transform an "action" token into a method name
     *
     * @param  string $action
     * @return string
     */
    public static function getMethodFromAction($action)
    {
        $method  = str_replace(array('.', '-', '_'), ' ', $action);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        $method  = lcfirst($method);
        $method .= 'Action';

        return $method;
    }
}
