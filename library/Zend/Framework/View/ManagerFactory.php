<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Application\Config\ServicesTrait as ApplicationConfig;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\View\Helper as ViewHelper;

class ManagerFactory
    extends Factory
{
    /**
     *
     */
    use ApplicationConfig;

    /**
     * @param Request $request
     * @param array $options
     * @return Manager
     */
    public function __invoke(Request $request, array $options = [])
    {
        return new Manager(new Config($this->config()['view_manager']), $this->sm->services());
    }
}
