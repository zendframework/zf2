<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Http;

use Zend\Framework\Response\Send\EventInterface;
use Zend\Framework\Response\Send\ContentTrait;
use Zend\Framework\Response\Send\HeadersTrait;
use Zend\Stdlib\ResponseInterface as Response;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ContentTrait,
        HeadersTrait;

    /**
     * @param EventInterface $event
     * @param Response $response
     * @return bool
     */
    public function __invoke(EventInterface $event, Response $response)
    {
        $this->sendHeaders($event, $response)
             ->sendContent($event, $response);

        return true;
    }
}
