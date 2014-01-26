<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Response;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\Response\Event as Response;
use Zend\Framework\Response\ServiceTrait as ResponseService;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventManager,
        EventListener,
        ResponseService;

    /**
     * @param EventInterface $event
     * @param string $response
     * @return mixed
     */
    public function trigger(EventInterface $event, $response)
    {
        $this->response->setContent($response);

        return $this->em->trigger(new Response, $this->response);
    }
}
