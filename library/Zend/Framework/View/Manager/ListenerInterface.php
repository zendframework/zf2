<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Event\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
    /**
     * @param $name
     * @return string
     */
    public function alias($name);

    /**
     * @param string $name
     * @param string $class
     */
    public function configure($name, $class);

    /**
     * @param string $name
     * @param array $options
     * @return object
     */
    public function get($name, array $options = []);

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function trigger(EventInterface $event);
}
