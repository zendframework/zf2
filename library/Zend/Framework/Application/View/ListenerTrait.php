<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\View;

use Exception;
use Zend\Framework\Application\EventInterface;
use Zend\Framework\Event\ListenerTrait as Listener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\View\Error\Event as Error;
use Zend\Framework\View\Event as View;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager,
        Listener;

    /**
     * @var EventInterface
     */
    protected $event;

    /**
     * @param EventInterface $event
     * @param Exception $exception
     * @return self
     */
    public function error(EventInterface $event, Exception $exception)
    {
        $error = new Error;

        $error->setTarget($event->target())
              ->setException($exception);

        $this->em->__invoke($error);

        $event->setResult($error->result());

        return $this;
    }

    /**
     * @param EventInterface $event
     * @return self
     */
    public function render(EventInterface $event)
    {
        $render = new View;

        $render->setTarget($event->viewModel());

        $this->em->__invoke($render);

        return $render->result();
    }
}
