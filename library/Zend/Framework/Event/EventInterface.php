<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event;

interface EventInterface
{
    /**
     * @param $listener
     * @param $options
     * @return mixed
     */
    public function call($listener, $options);

    /**
     * @return string|array
     */
    public function name();

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
