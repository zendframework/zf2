<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Route\EventInterface as Event;
use Zend\Stdlib\RequestInterface as Request;

interface EventInterface
    extends Event
{
    /**
     * @return int
     */
    public function baseUrlLength();

    /**
     * @return array
     */
    public function options();

    /**
     * @return int
     */
    public function pathLength();

    /**
     * @return Request
     */
    public function request();
}
