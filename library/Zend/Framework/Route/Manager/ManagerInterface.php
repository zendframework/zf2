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
    public function match(Request $request);

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function route($name, $options = null);

    /**
     * @return ConfigInterface
     */
    public function routes();
}
