<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\EventManager\ListenerInterface as Listener;

interface ListenerInterface
    extends Listener
{
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
     * @return mixed
     */
    public function config($name);

    /**
     * @param ListenerConfigInterface $config
     * @return $this
     */
    public function configuration(ListenerConfigInterface $config);

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
    public function __invoke(EventInterface $event);
}
