<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use ReflectionClass;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\ManagerInterface as ServiceManager;
use Zend\Framework\Service\ServiceInterface;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\Service\ServiceTrait as Service;

class InstanceFactory
    implements FactoryInterface
{
    /**
     *
     */
    use EventListener,
        Service;

    /**
     * @param ServiceManager $sm
     * @param string|callable $factory
     */
    public function __construct(ServiceManager $sm, $factory)
    {
        $this->sm      = $sm;
        $this->factory = $factory;
    }

    /**
     * @param Request $request
     * @param array $options
     * @return mixed|object
     */
    public function service(Request $request, array $options = [])
    {
        if ($options) {

            $instance = (new ReflectionClass($this->factory))->newInstanceArgs($options);

        } else {

            $instance = new $this->factory; //could be anything

        }

        if ($instance instanceof ServiceInterface) {
            $instance->__service($this->sm);
        }

        return $instance;
    }
}
