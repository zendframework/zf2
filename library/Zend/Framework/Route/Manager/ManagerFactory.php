<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Route\ServicesConfigTrait as Config;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\View\Helper as ViewHelper;

class ManagerFactory
    extends Factory
{
    /**
     *
     */
    use Config;

    /**
     * @param Request $request
     * @param array $options
     * @return Manager
     */
    public function __invoke(Request $request, array $options = [])
    {
        return new Manager($this->routerConfig()->config(), $this->services());
    }
}
