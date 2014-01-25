<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Console;

use Zend\Console\Response;
use Zend\Framework\Response\EventInterface;

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
     * Send the response
     *
     * @param  EventInterface $event
     * @param $response
     * @return void
     */
    public function trigger(EventInterface $event, $response = null)
    {
        if (!$response instanceof Response) {
            return;
        }

        $this->sendContent($event, $response);

        $errorLevel = (int) $response->getMetadata('errorLevel',0);

        exit($errorLevel);
    }
}
