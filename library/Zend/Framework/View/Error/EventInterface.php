<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Error;

use Exception;
use Zend\Framework\Event\EventInterface as Event;

interface EventInterface
    extends Event
{
    /**
     * @param Exception $exception
     * @return self
     */
    public function setException(Exception $exception);
}
