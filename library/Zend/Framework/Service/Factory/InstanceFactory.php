<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use ReflectionClass;
use Zend\Framework\Service\RequestInterface;
use Zend\Framework\Service\Manager\ManagerInterface;
use Zend\Framework\Service\ServiceInterface;

class InstanceFactory
    implements FactoryInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @var string
     */
    protected $factory;

    /**
     * @param ManagerInterface $sm
     * @param string $factory
     */
    public function __construct(ManagerInterface $sm, $factory)
    {
        $this->sm      = $sm;
        $this->factory = $factory;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return mixed|object
     */
    public function __invoke(RequestInterface $request, array $options = [])
    {

        if ($options) {

            $class = new ReflectionClass($this->factory);

            $instance = $class->hasMethod('__construct') ? $class->newInstanceArgs($options) : $class->newInstance();

        } else {

            $instance = new $this->factory; //could be anything

        }

        if ($instance instanceof ServiceInterface) {
            $instance->__service($this->sm);
        }

        return $instance;
    }
}
