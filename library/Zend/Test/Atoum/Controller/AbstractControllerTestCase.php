<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace Zend\Test\Atoum\Controller;

use mageekguy\atoum;
use Zend\Test\Atoum\Exception\ExpectationFailedException;
use Zend\Test\Gateway\ControllerTestCaseInterface;
use Zend\Test\Gateway\SharedService;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage Atoum
 */
class AbstractControllerTestCase extends atoum\test implements ControllerTestCaseInterface
{
    protected $sharedService;
    
    public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
    {
        $namespace = substr(get_class($this), 0, strrpos(get_class($this), '\\'));
        $class = explode('\\', get_class($this));
        $className = end($class);
        $this->setTestNamespace($namespace);
        spl_autoload_register(function($class) use ($className) {
            if($class == $className) {
                eval("namespace{ class $className{}; }");
                return true;
            }
            return false;
        });
        parent::__construct($score, $locale, $adapter);
    }
    
    public function getSharedService()
    {
        if(null === $this->sharedService) {
            $this->sharedService = new SharedService();
        }
        return $this->sharedService;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->getSharedService(), $name)) {
            return call_user_func_array(array($this->getSharedService(), $name), $arguments);
        }
        return parent::__call($name, $arguments);
    }

    /**
     * Assert the modules loaded with the module manager
     *
     * @param  array $modules
     * @return void
     */
    public function assertModulesLoaded(array $modules)
    {
        $moduleManager = $this->getApplicationServiceLocator()->get('ModuleManager');
        $modulesLoaded = $moduleManager->getModules();
        $list = array_diff($modules, $modulesLoaded);
        if($list) {
            throw new ExpectationFailedException(sprintf(
                'Several modules are not loaded "%s"', implode(', ', $list)
            ));
        }
        $this->array($list)->isEmpty();
    }

    /**
     * Assert the modules loaded with the module manager
     *
     * @param  array $modules
     * @return void
     */
    public function assertNotModulesLoaded(array $modules)
    {
        $moduleManager = $this->getApplicationServiceLocator()->get('ModuleManager');
        $modulesLoaded = $moduleManager->getModules();
        $list = array_intersect($modules, $modulesLoaded);
        if($list) {
            throw new ExpectationFailedException(sprintf(
                'Several modules WAS not loaded "%s"', implode(', ', $list)
            ));
        }
        $this->array($list)->isEmpty();
    }

    /**
     * Assert response status code
     *
     * @param  int $code
     * @return void
     */
    public function assertResponseStatusCode($code)
    {
        if($this->getUseConsoleRequest()) {
            if(!in_array($code, array(0, 1))) {
                throw new ExpectationFailedException(
                    'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if($code != $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting response code "%s", actual status code is "%s"',
                $code,
                $match
            ));
        }
        $this->integer($code)->isEqualTo($match);
    }

    /**
     * Assert response status code
     *
     * @param  int $code
     * @return void
     */
    public function assertNotResponseStatusCode($code)
    {
        if($this->getUseConsoleRequest()) {
            if(!in_array($code, array(0, 1))) {
                throw new ExpectationFailedException(
                    'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if($code == $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting response code was NOT "%s"',
                $code
            ));
        }
        $this->integer($code)->isNotEqualTo($match);
    }

    /**
     * Assert that the application route match used the given module
     *
     * @param  string $module
     * @return void
     */
    public function assertModule($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $match = strtolower($match);
        $module = strtolower($module);
        if($module != $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting module "%s", actual module is "%s"',
                $module,
                $match
            ));
        }
        $this->string($module)->isEqualTo($match);
    }

    /**
     * Assert that the application route match used NOT the given module
     *
     * @param  string $module
     * @return void
     */
    public function assertNotModule($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $match = strtolower($match);
        $module = strtolower($module);
        if($module == $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting module was NOT "%s"',
                $module
            ));
        }
        $this->string($module)->isNotEqualTo($match);
    }

    /**
     * Assert that the application route match used the given controller class
     *
     * @param  string $controller
     * @return void
     */
    public function assertControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, strrpos($controllerClass, '\\')+1);
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller != $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting controller class "%s", actual controller class is "%s"',
                $controller,
                $match
            ));
        }
        $this->string($controller)->isEqualTo($match);
    }

    /**
     * Assert that the application route match used NOT the given controller class
     *
     * @param  string $controller
     * @return void
     */
    public function assertNotControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, strrpos($controllerClass, '\\')+1);
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller == $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting controller class was NOT "%s"',
                $controller
            ));
        }
        $this->string($controller)->isNotEqualTo($match);
    }

    /**
     * Assert that the application route match used the given controller name
     *
     * @param  string $controller
     * @return void
     */
    public function assertControllerName($controller)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('controller');
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller != $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting controller name "%s", actual controller is "%s"',
                $controller,
                $match
            ));
        }
        $this->string($controller)->isEqualTo($match);
    }

    /**
     * Assert that the application route match used NOT the given controller name
     *
     * @param  string $controller
     * @return void
     */
    public function assertNotControllerName($controller)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('controller');
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller == $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting controller name was NOT "%s"',
                $controller
            ));
        }
        $this->string($controller)->isNotEqualTo($match);
    }

    /**
     * Assert that the application route match used the given action
     *
     * @param  string $action
     * @return void
     */
    public function assertActionName($action)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('action');
        $match = strtolower($match);
        $action = strtolower($action);
        if($action != $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting action name "%s", actual action is "%s"',
                $action,
                $match
            ));
        }
        $this->string($action)->isEqualTo($match);
    }

    /**
     * Assert that the application route match used NOT the given action
     *
     * @param  string $action
     * @return void
     */
    public function assertNotActionName($action)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('action');
        $match = strtolower($match);
        $action = strtolower($action);
        if($action == $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting action name was NOT "%s"',
                $action
            ));
        }
        $this->string($action)->isNotEqualTo($match);
    }

    /**
     * Assert that the application route match used the given route name
     *
     * @param  string $route
     * @return void
     */
    public function assertMatchedRouteName($route)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getMatchedRouteName();
        $match = strtolower($match);
        $route = strtolower($route);
        if($route != $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting matched route was "%s", actual route is "%s"',
                $route,
                $match
            ));
        }
        $this->string($route)->isEqualTo($match);
    }

    /**
     * Assert that the application route match used NOT the given route name
     *
     * @param  string $route
     * @return void
     */
    public function assertNotMatchedRouteName($route)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getMatchedRouteName();
        $match = strtolower($match);
        $route = strtolower($route);
        if($route == $match) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting route matched was NOT "%s"', $route
            ));
        }
        $this->string($route)->isNotEqualTo($match);
    }

    /**
     * Assert against DOM selection
     *
     * @param  string $path CSS selector path
     * @return void
     */
    public function assertQuery($path)
    {
        $match = $this->query($path);
        if(!$match > 0) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s EXISTS', $path
            ));
        }
        $this->integer($match)->isGreaterThan(0);
    }

    /**
     * Assert against DOM selection
     *
     * @param  string $path CSS selector path
     * @return void
     */
    public function assertNotQuery($path)
    {
        $match = $this->query($path);
        if($match != 0) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s DOES NOT EXIST', $path
            ));
        }
        $this->integer($match)->isEqualTo(0);
    }

    /**
     * Assert against DOM selection; should contain exact number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should match
     * @return void
     */
    public function assertQueryCount($path, $count)
    {
        $match = $this->query($path);
        if($match != $count) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s OCCURS EXACTLY %d times',
                $path, $count
            ));
        }
        $this->integer($count)->isEqualTo($match);
    }

    /**
     * Assert against DOM selection; should NOT contain exact number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should NOT match
     * @return void
     */
    public function assertNotQueryCount($path, $count)
    {
        $match = $this->query($path);
        if($match == $count) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s DOES NOT OCCUR EXACTLY %d times',
                $path, $count
            ));
        }
        $this->integer($count)->isNotEqualTo($match);
    }

    /**
     * Assert against DOM selection; should contain at least this number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Minimum number of nodes that should match
     * @return void
     */
    public function assertQueryCountMin($path, $count)
    {
        $match = $this->query($path);
        if($match < $count) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s OCCURS AT LEAST %d times',
                $path, $count
            ));
        }
        $this->integer($match)->isGreaterThanOrEqualTo($count);
    }

    /**
     * Assert against DOM selection; should contain no more than this number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Maximum number of nodes that should match
     * @return void
     */
    public function assertQueryCountMax($path, $count)
    {
        $match = $this->query($path);
        if($match > $count) {
            throw new ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s OCCURS AT MOST %d times',
                $path, $count
            ));
        }
        $this->integer($match)->isLessThanOrEqualTo($count);
    }
}
