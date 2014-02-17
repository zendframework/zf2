<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\ManagerInterface as ServiceManager;
use Zend\Framework\Service\ServiceTrait as Service;

class AbstractFactory
    implements FactoryInterface
{
    /**
     *
     */
    use Service;

    /**
     * @param ServiceManager $sm
     * @param array $factory
     */
    public function __construct(ServiceManager $sm, $factory)
    {
        $this->sm      = $sm;
        $this->factory = $factory;
    }

    /**
     * @param $request
     * @param array $options
     * @return mixed|object
     */
    public function call($request, array $options = [])
    {
        return $this->__invoke($request, $options);
    }

    /**
     * @param Request $request
     * @param array $options
     * @return mixed|object
     */
    public function __invoke(Request $request, array $options = [])
    {
        return (new $this->factory[0]($this->sm, $this->factory[1]))->__invoke($request, $options);
    }
}
