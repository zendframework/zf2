<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Send;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Framework\Response\ServiceTrait as ResponseTrait;
use Zend\Framework\Event\EventTrait as EventTrait;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        ResponseTrait;

    /**
     * Provides service name
     */
    const EVENT = 'Event\Response\Send';

    /**
     * @param callable $listener
     * @param Response $response
     * @return mixed
     */
    public function __invoke(callable $listener, Response $response)
    {
        $response = $listener($this, $response);

        if ($response) {
            $this->stop();
        }

        return $response;
    }
}
