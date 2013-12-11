<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\EventManager\EventInterface;
use Zend\View\Model\ClearableModelInterface;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener extends EventListener
{

    protected $name = [
        MvcEvent::EVENT_CONTROLLER_DISPATCH,
        MvcEvent::EVENT_DISPATCH_ERROR,
        MvcEvent::EVENT_RENDER_ERROR
    ];

    protected $priority = -100;

    public function __invoke(EventInterface $event)
    {
        $result = $event->getResult();

        if (!$result instanceof ViewModel) {
            return;
        }

        $model = $event->getViewModel();

        if ($result->terminate()) {
            $event->setViewModel($result);
            return;
        }

        if ($event->getError() && $model instanceof ClearableModelInterface) {
            $model->clearChildren();
        }

        $model->addChild($result);
    }
}
