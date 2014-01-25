<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\Route\ListenerInterface as Listener;
use Zend\Framework\Route\EventInterface;

interface ListenerInterface
    extends Listener
{
    /**
     *
     */
    const MODULE_NAMESPACE    = '__NAMESPACE__';

    /**
     *
     */
    const ORIGINAL_CONTROLLER = '__CONTROLLER__';

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @param mixed $response
     * @return mixed
     */
    public function trigger(EventInterface $event, $response);
}
