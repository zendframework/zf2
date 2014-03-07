<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

interface EventInterface
{
    /**
     * Event name
     *
     * @return string
     */
    public function event();

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function signal(callable $listener, $options = null);

    /**
     * @return mixed
     */
    public function source();

    /**
     * @return self
     */
    public function stop();

    /**
     * @return bool
     */
    public function stopped();
}
