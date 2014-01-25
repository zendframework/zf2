<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Template;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Module\Route\EventListenerInterface as RouteListener;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @var string
     */
    protected $name = self::EVENT_TEMPLATE;

    /**
     * Target
     *
     * @var mixed
     */
    protected $target = self::WILDCARD;

    /**
     * @param EventInterface $event
     * @param $response
     * @return mixed
     */
    public function trigger(EventInterface $event, $response)
    {
        $model = $response;
        if (!$model instanceof ViewModel) {
            return $model;
        }

        $template = $model->getTemplate();

        if (!empty($template)) {
            return $model;
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

        return $model;
    }
}
