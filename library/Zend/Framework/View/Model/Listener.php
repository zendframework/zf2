<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\Event\EventInterface;
use Zend\View\Model\ClearableModelInterface as ClearableModel;
use Zend\Framework\Event\ListenerTrait as EventListener;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventListener,
        ServiceTrait;

    /**
     * @param EventInterface $event
     * @param mixed $result
     * @return mixed|void
     */
    public function trigger(EventInterface $event, $result)
    {
        if (!$result instanceof ViewModel) {
            return $result;
        }

        if ($result->terminate()) {
            $this->viewModel = $result;
            return $result;
        }

        //if ($event->error() && $this->viewModel instanceof ClearableModel) {
            //$this->viewModel->clearChildren();
        //}

        $this->viewModel->addChild($result);

        return $this->viewModel;
    }
}
