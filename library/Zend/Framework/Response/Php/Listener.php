<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Php;

use Zend\Framework\Response\EventInterface;
use Zend\Framework\Response\Send\ContentTrait;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ContentTrait;

    /**
     * @param  EventInterface $event
     * @param Response $response
     * @return self
     */
    public function __invoke(EventInterface $event, Response $response)
    {
        $this->sendContent($event, $response);
        $errorLevel = (int) $response->getMetadata('errorLevel',0);

        exit($errorLevel);
    }
}
