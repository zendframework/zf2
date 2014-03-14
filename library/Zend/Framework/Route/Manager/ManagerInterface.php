<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Route\Config\ConfigInterface;
use Zend\Stdlib\RequestInterface as Request;

interface ManagerInterface
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function route(Request $request);

    /**
     * @return ConfigInterface
     */
    public function routes();
}
