<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use ArrayAccess;

/**
 * Representation of an listener
 */
interface CallbackListenerInterface extends ListenerInterface
{
    /**
     * Callback used for this listener
     *
     * @param $callback
     * @return void
     */
    public function setCallback($callback);
}
