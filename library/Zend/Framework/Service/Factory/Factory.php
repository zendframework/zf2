<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Exception;
use Zend\Framework\Service\ConfigInterface as Config;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\ManagerInterface as ServiceManager;
use Zend\Framework\Service\ServiceTrait as Service;

class Factory
    implements FactoryInterface
{
    /**
     *
     */
    use Service;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @return Config
     */
    public function config()
    {
        return $this->sm->config();
    }

    /**
     * @param Request $request
     * @param array $options
     * @return mixed|void
     * @throws Exception
     */
    public function __invoke(Request $request, array $options = [])
    {
        throw new Exception('Missing service method for ' . get_class($this));
    }
}
