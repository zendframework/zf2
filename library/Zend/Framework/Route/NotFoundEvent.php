<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\EventManager\Event as EventManagerEvent;

class NotFoundEvent extends EventManagerEvent
{
    protected $name = 'dispatch.error';

    protected $error = 'error-router-no-match';

    public function getError()
    {
        return $this->error;
    }
}
