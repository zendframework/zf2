<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Route\Manager\ServicesTrait as Router;
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
    public function __invoke(Request $request, array $options = [])
    {
        return (new Listener)->setRouter($this->router());
    }
}