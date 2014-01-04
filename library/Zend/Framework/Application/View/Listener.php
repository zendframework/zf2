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
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener
    implements ListenerInterface, EventListenerInterface
{
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
    public function __construct($event = self::EVENT_APPLICATION, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        /** @var \Zend\Framework\Application\Event $event */
        /** @var \Zend\Framework\Application\Listener $em */
        /** @var \Zend\Framework\Application\Service\Listener $sm */

        $this->sm = $event->serviceManager();
        $this->em = $event->eventManager();

        $result = $event->result();

        if ($result instanceof Response || !$result instanceof ViewModel) {
            return;
        }

        try {

            $this->render($event);

        } catch(Exception $exception) {

            $this->error($event, $exception);

        }
    }
}
