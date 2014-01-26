<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\View\Helper as ViewHelper;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use Config;

    /**
     * @param Request $request
     * @return Listener
     */
    public function service(Request $request)
    {
        $config = $this->appConfig()['view_manager'];

        $vm = new Listener($config, $this->sm);

        return $vm;
    }
}
