<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Php;

use Zend\Framework\Response\EventInterface;
use Zend\Framework\Response\ListenerTrait as ResponseListener;
use Zend\Framework\Response\SendContentTrait as SendContent;
use Zend\Framework\Response\SendHeadersTrait as SendHeaders;
use Zend\Http\Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ResponseListener,
        SendContent,
        SendHeaders;

    /**
     * @param  EventInterface $event
     * @param $response
     * @return self
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
