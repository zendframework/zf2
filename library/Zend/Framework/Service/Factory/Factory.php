<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use RuntimeException;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManager;

class Factory
    implements FactoryInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param Request $request
     * @param array $options
     * @return mixed|void
     * @throws RuntimeException
     */
    public function __invoke(Request $request, array $options = [])
    {
        throw new RuntimeException('Missing service method for ' . get_class($this));
    }
}
