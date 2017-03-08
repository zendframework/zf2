<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Mvc\Controller\ActionController;
use Zend\Mvc\MvcEvent;
use Zend\Json\Server\Server;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Abstract controller for Json based RPC
 * 
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class JsonRpcController extends ActionController
{

    /**
     * Must return an array of strings. Each string must be a method name
     * of the concrete class. These methods are the RPC methods
     *
     * @return array
     */
    abstract public function registerRpcMethods();
    
    /**
     * Routes the request to the correct method. If it is a 'get' request, it will
     * return the serviceMap. If it is a 'post' request, it will call the relevant
     * RPC method. Note that in using this controller there is no need for an 
     * 'action' param in the routeMatch object.
     * 
     * @param MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function execute(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }

        $request = $event->getRequest();

        switch (strtolower($request->getMethod())) {
            case 'get':
                //get the RPC SMD
                $return = $this->getServiceMap();
                break;
            case 'post':
                //use the RPC method
                $return = $this->handle($request);
                break;
            default:
                throw new Exception\DomainException('Invalid HTTP method!');
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a listener returns a response object, return it immediately
        $event->setResult($return);
        return $return;
    }

    /**
     * Construct the RPC serice map. This can be used by javascript frameworks
     * to create a json RPC client. The service map will include all methods 
     * returned by self::registerRpcMethods. This method uses Zend\Json\Server\Server
     * to help construct the service map. Docs for Zend\Json\Server\Server include 
     * complete information about how to set up RPC methods, but it is worth noting
     * that typehints and the docblock are significant.
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function getServiceMap(){
        
        $server = new Server;      
        $class = get_class($this);
        foreach ($this->registerRpcMethods() as $method){
            $server->addFunction(array($class, $method));
        }
        $serviceMap = $server->getServiceMap();
        return new JsonModel($serviceMap->toArray());
    } 
    
    /**
     * Will process an RPC method call. Errors are caught and returned
     * to the client.
     * 
     * @param \Zend\Stdlib\RequestInterface $request
     * @return \Zend\View\Model\JsonModel
     */
    public function handle(Request $request){
        
        $content = json_decode($request->getContent(), true);
        $method = $content['method'];
        $rpcMethods = $this->registerRpcMethods();
        $error = null;
        
        if(in_array($method, $rpcMethods)){      
            try {
                $result = call_user_func_array(array($this, $method), $content['params']);
            } catch (\Exception $e){
                $result = null;
                $error = $e->getMessage();
            }
        } else {
            $result = null;
            $error = 'Method '. $method .' not found';          
        }
        
        return new JsonModel(array(
            'id' => $content['id'],
            'result' => $result,
            'error' => $error
        ));
    }   
}
