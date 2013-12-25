<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\EventManager\EventInterface;
use Zend\View\Model\ClearableModelInterface as ClearableModel;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    const MODEL_PRIORITY = -80;
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
    public function __construct($event = self::EVENT_MODEL, $target = null, $priority = self::MODEL_PRIORITY)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     */
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

        if ($event->getError() && $model instanceof ClearableModel) {
            $model->clearChildren();
        }

        $model->addChild($result);
    }
}
