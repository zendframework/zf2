<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManager;

class AbstractFactory
    implements FactoryInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @var array
     */
    protected $factory;

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
     * @param Request $request
     * @param array $options
     * @return mixed|object
     */
    public function __invoke(Request $request, array $options = [])
    {
        return $this->get([$this->factory[0], $this->sm, $this->factory[1]])->__invoke($request, $options);
    }
}
