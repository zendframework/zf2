<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch;

use Zend\Framework\Application\EventListenerInterface as EventListener;

interface EventListenerInterface
    extends EventListener
{
    /**
     *
     */
    const EVENT_DISPATCH_ERROR = 'mvc.dispatch.error';
}
