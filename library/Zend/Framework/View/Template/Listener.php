<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Template;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\Module\Route\EventListenerInterface as RouteListener;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_TEMPLATE, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        $model = $event->result();
        if (!$model instanceof ViewModel) {
            return;
        }

        $template = $model->getTemplate();

        if (!empty($template)) {
            return;
        }

        $routeMatch = $event->routeMatch();
        $controller = $event->target();
        if (is_object($controller)) {
            $controller = get_class($controller);
        }
        if (!$controller) {
            $controller = $routeMatch->getParam('controller', '');
        }

        $module = $this->deriveModuleNamespace($controller);

        if ($namespace = $routeMatch->getParam(RouteListener::MODULE_NAMESPACE)) {
            $controllerSubNs = $this->deriveControllerSubNamespace($namespace);
            if (!empty($controllerSubNs)) {
                if (!empty($module)) {
                    $module .= '/' . $controllerSubNs;
                } else {
                    $module = $controllerSubNs;
                }
            }
        }

        $controller = $this->deriveControllerClass($controller);
        $template   = $this->inflectName($module);

        if (!empty($template)) {
            $template .= '/';
        }
        $template  .= $this->inflectName($controller);

        $action     = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);
    }
}
