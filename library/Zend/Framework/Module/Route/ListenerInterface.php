<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\EventManager\ListenerInterface as Listener;
use Zend\Framework\Route\EventInterface;

interface ListenerInterface
    extends Listener
{
    /**
     * Invokes listener with the event
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event);
}
