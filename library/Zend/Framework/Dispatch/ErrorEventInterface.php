<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Exception;
use Zend\Framework\EventManager\EventInterface as Event;

interface ErrorEventInterface
    extends Event
{
    /**
     *
     */
    const EVENT_DISPATCH_ERROR = 'mvc.dispatch.error';

    /**
     * @param Exception $exception
     * @return $this
     */
    public function setException(Exception $exception);

    /**
     * @param $name
     * @return $this
     */
    public function setControllerName($name);

    /**
     * @param $className
     * @return $this
     */
    public function setControllerClass($className);
}
