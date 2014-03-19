<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Uri\Http as Uri;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT = 'Event\Route\Http';

    /**
     * @return int
     */
    public function pathLength();

    /**
     * @return int
     */
    public function pathOffset();

    /**
     * @return Uri
     */
    public function uri();
}
