<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory\Service;

use ReflectionClass;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\ListenerInterface;
use Zend\Framework\Service\ListenerTrait as Listener;
use Zend\Framework\Service\ServiceInterface;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * @var string|callable
     */
    protected $factory;

    /**
     * @param EventInterface $event
     * @return mixed|object
     */
    public function __invoke(EventInterface $event)
    {
        $options = $event->options();

        if (is_subclass_of($this->factory, ListenerInterface::class)) {
            if (!is_object($this->factory)) {
                $this->factory = new $this->factory($this->sm);
            }

            return $event->__invoke($this->factory);
        }

        if (is_callable($this->factory)) {
            return call_user_func_array($this->factory, $event);
        }

        if ($options) {

            $class = new ReflectionClass($this->factory);
            $instance = $class->newInstanceArgs($options);

        } else {

            $instance = new $this->factory; //could be anything

        }

        if ($instance instanceof ServiceInterface) {
           $instance->__service($this->sm);
        }

        return $instance;
    }
}
