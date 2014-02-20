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

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @param EventInterface $event
     * @param mixed $result
     * @return mixed|void
     */
    public function __invoke(EventInterface $event, $result)
    {
        var_dump(__FILE__);

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
