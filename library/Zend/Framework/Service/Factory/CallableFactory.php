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

class CallableFactory
    implements FactoryInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @var callable
     */
    protected $factory;

    /**
     * @param ServiceManager $sm
     * @param callable $factory
     */
    public function __construct(ServiceManager $sm, callable $factory)
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
        return call_user_func_array($this->factory, array_merge([$this->sm, $request], $options));
    }
}
