<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\Event\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function trigger(EventInterface $event);
}
