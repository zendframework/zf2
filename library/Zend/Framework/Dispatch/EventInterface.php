<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

interface EventInterface
{
    /**
     *
     */
    const EVENT_DISPATCH = 'mvc.dispatch';

    /**
     *
     */
    const EVENT_DISPATCH_ERROR = 'mvc.dispatch.error';
}
