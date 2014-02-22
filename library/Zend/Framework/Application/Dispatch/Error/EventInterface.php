<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch\Error;

use Exception;
use Zend\Framework\Event\EventInterface as Event;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT = 'Dispatch\Error';

    /**
     * @return Exception
     */
    public function exception();
}
