<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\View;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\View\Exception\EventInterface as Exception;
use Zend\Framework\View\Renderer\EventInterface as View;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventManager;

    /**
     * @param EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        if (!$event->viewModel()) {
            return null;
        }

        try {

            return $this->trigger(View::EVENT, $event->viewModel());

        } catch(\Exception $exception) {

            $viewModel = $this->trigger([Exception::EVENT, $exception], $event->viewModel());

            return $this->trigger(View::EVENT, $viewModel);
        }
    }
}
