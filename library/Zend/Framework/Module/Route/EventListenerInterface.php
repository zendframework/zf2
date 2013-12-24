<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

interface EventListenerInterface
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
     *
     */
    const EVENT_ROUTE = 'mvc.route';
}
