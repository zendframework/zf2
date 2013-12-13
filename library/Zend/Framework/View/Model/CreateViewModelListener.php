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
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

use Zend\Framework\EventManager\Listener as EventListener;

class CreateViewModelListener
    extends EventListener
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_CONTROLLER_DISPATCH;

    /**
     * @var int
     */
    protected $priority = -80;

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $result = $event->getResult();

        //create from null
        if (null === $result) {
            $event->setResult(new ViewModel);
            return;
        }

        //create from array
        if (!ArrayUtils::hasStringKeys($result, true)) {
            return;
        }

        $event->setResult(new ViewModel($result));
    }
}
