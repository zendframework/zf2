<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\EventInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

class CreateViewModelListener extends EventListener
{

    protected $name = MvcEvent::EVENT_CONTROLLER_DISPATCH;

    protected $priority = -80;

    public function __invoke(EventInterface $e)
    {
        $result = $e->getResult();

        //create from null
        if (null === $result) {
            $e->setResult(new ViewModel);
            return;
        }

        //create from array
        if (!ArrayUtils::hasStringKeys($result, true)) {
            return;
        }

        $e->setResult(new ViewModel($result));
    }
}
