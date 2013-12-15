<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\MvcEvent;
use Zend\View\Model\ClearableModelInterface as ClearableModel;
use Zend\View\Model\ModelInterface as ViewModel;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends EventListener
    implements FactoryInterface
{
    /**
     * @var array
     */
    protected $name = [
        MvcEvent::EVENT_CONTROLLER_DISPATCH,
        MvcEvent::EVENT_DISPATCH_ERROR,
        MvcEvent::EVENT_RENDER_ERROR
    ];

    /**
     * @var int
     */
    //protected $priority = -100;
    protected $priority = -80;

    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    /**
     * @param Event $event
     * @return mixed|void
     */
    public function __invoke(Event $event)
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

        if ($event->getError() && $model instanceof ClearableModel) {
            $model->clearChildren();
        }

        $model->addChild($result);
    }
}
