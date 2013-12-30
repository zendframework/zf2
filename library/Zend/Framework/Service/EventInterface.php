<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\EventManager\EventInterface as Event;

interface EventInterface
    extends Event
{

    /**
     * @return string
     */
    public function service();

    /**
     * @return array
     */
    public function options();

    /**
     * @return bool|object
     */
    public function instance();

    /**
     * @return ListenerInterface
     */
    public function listener();

    /**
     * @return string|callable
     */
    public function factory();
}
