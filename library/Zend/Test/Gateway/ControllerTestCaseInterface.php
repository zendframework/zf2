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

/**
 * @category   Zend
 * @package    Zend_Test
 */
interface ControllerTestCaseInterface
{
    /**
     * Assert the modules loaded with the module manager
     *
     * @param  array $modules
     * @return void
     */
    public function assertModulesLoaded(array $modules);

    /**
     * Assert the modules loaded with the module manager
     *
     * @param  array $modules
     * @return void
     */
    public function assertNotModulesLoaded(array $modules);

    /**
     * Assert response status code
     *
     * @param  int $code
     * @return void
     */
    public function assertResponseStatusCode($code);

    /**
     * Assert response status code
     *
     * @param  int $code
     * @return void
     */
    public function assertNotResponseStatusCode($code);

    /**
     * Assert that the application route match used the given module
     *
     * @param  string $module
     * @return void
     */
    public function assertModule($module);

    /**
     * Assert that the application route match used NOT the given module
     *
     * @param  string $module
     * @return void
     */
    public function assertNotModule($module);

    /**
     * Assert that the application route match used the given controller class
     *
     * @param  string $controller
     * @return void
     */
    public function assertControllerClass($controller);

    /**
     * Assert that the application route match used NOT the given controller class
     *
     * @param  string $controller
     * @return void
     */
    public function assertNotControllerClass($controller);

    /**
     * Assert that the application route match used the given controller name
     *
     * @param  string $controller
     * @return void
     */
    public function assertControllerName($controller);

    /**
     * Assert that the application route match used NOT the given controller name
     *
     * @param  string $controller
     * @return void
     */
    public function assertNotControllerName($controller);

    /**
     * Assert that the application route match used the given action
     *
     * @param  string $action
     * @return void
     */
    public function assertActionName($action);

    /**
     * Assert that the application route match used NOT the given action
     *
     * @param  string $action
     * @return void
     */
    public function assertNotActionName($action);

    /**
     * Assert that the application route match used the given route name
     *
     * @param  string $route
     * @return void
     */
    public function assertMatchedRouteName($route);

    /**
     * Assert that the application route match used NOT the given route name
     *
     * @param  string $route
     * @return void
     */
    public function assertNotMatchedRouteName($route);
    
    /**
     * Assert against DOM selection
     *
     * @param  string $path CSS selector path
     * @return void
     */
    public function assertQuery($path);

    /**
     * Assert against DOM selection
     *
     * @param  string $path CSS selector path
     * @return void
     */
    public function assertNotQuery($path);

    /**
     * Assert against DOM selection; should contain exact number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should match
     * @return void
     */
    public function assertQueryCount($path, $count);

    /**
     * Assert against DOM selection; should NOT contain exact number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should NOT match
     * @return void
     */
    public function assertNotQueryCount($path, $count);

    /**
     * Assert against DOM selection; should contain at least this number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Minimum number of nodes that should match
     * @return void
     */
    public function assertQueryCountMin($path, $count);

    /**
     * Assert against DOM selection; should contain no more than this number of nodes
     *
     * @param  string $path CSS selector path
     * @param  string $count Maximum number of nodes that should match
     * @return void
     */
    public function assertQueryCountMax($path, $count);
}
