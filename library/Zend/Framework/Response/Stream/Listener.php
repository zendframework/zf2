<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Stream;

use Zend\Framework\Response\EventInterface;
use Zend\Http\Response\Stream;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @var string
     */
    protected $name = self::EVENT_RESPONSE;

    /**
     * Target
     *
     * @var mixed
     */
    protected $target = self::WILDCARD;

    /**
     * Send stream response
     *
     * @param  EventInterface $event
     * @param $response
     * @return self
     */
    public function trigger(EventInterface $event, $response = null)
    {
        if (!$response instanceof Stream) {
            return $this;
        }

        $this->sendHeaders($event, $response)
             ->sendStream($event, $response);

        return self::STOPPED;
    }
}
