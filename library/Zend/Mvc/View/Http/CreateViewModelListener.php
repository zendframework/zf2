<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\View\Http;

use Zend\Framework\EventManager\AbstractListenerAggregate;
use Zend\Framework\EventManager\ManagerInterface as EventManager;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

class CreateViewModelListener extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManager $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'createViewModelFromArray'),  -80);
        $this->listeners[] = $events->attach('dispatch', array($this, 'createViewModelFromNull'),   -80);
    }

    /**
     * Inspect the result, and cast it to a ViewModel if an assoc array is detected
     *
     * @param  Event $e
     * @return void
     */
    public function createViewModelFromArray(Event $e)
    {
        $result = $e->getResult();
        if (!ArrayUtils::hasStringKeys($result, true)) {
            return;
        }

        $model = new ViewModel($result);
        $e->setResult($model);
    }

    /**
     * Inspect the result, and cast it to a ViewModel if null is detected
     *
     * @param MvcEvent $e
     * @return void
    */
    public function createViewModelFromNull(Event $e)
    {
        $result = $e->getResult();
        if (null !== $result) {
            return;
        }

        $model = new ViewModel;
        $e->setResult($model);
    }
}
