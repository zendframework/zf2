<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\NotFound;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
    /**
     * @param EventInterface $event
     * @param mixed $result
     * @return mixed
     */
    public function trigger(EventInterface $event, $result);
}
