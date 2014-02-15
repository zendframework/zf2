<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Template;

use Zend\Framework\Controller\ListenerInterface as Controller;
use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Model\ModelInterface as ViewModel;

interface ListenerInterface
    extends Listener
{
    /**
     * @param ViewModel $model
     * @param Controller $controller
     * @param RouteMatch $routeMatch
     * @return mixed|ViewModel
     */
    public function __invoke(ViewModel $model, Controller $controller, RouteMatch $routeMatch);
}
