<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Console;

use Zend\Framework\Response\Send\EventInterface;
use Zend\Stdlib\ResponseInterface as Response;

interface ListenerInterface
{
    /**
     * @param EventInterface $event
     * @param Response $response
     */
    public function __invoke(EventInterface $event, Response $response);
}
