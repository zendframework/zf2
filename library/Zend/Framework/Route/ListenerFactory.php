<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Route\ServicesTrait as Router;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use Router;

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function service(Request $request, array $options = [])
    {
        $router = new Listener;

        $router->setRouter($this->router());

        return $router;
    }
}
