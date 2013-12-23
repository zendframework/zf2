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
use Zend\Framework\EventManager\ListenerTrait;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\View\Model\ClearableModelInterface as ClearableModel;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener
    implements ListenerInterface,
               FactoryInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * Name(s) of events to listener for
     *
     * @var string|array
     */
    protected $eventName = self::EVENT_MODEL;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = self::WILDCARD;

    /**
     * Priority of listener
     *
     * @var int
     */
    protected $eventPriority = -80;

    /**
     * @param ServiceManager $sm
     * @return $this|mixed
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
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
