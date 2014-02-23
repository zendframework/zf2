<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Service\Factory\FactoryInterface;

interface ManagerInterface
{
    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service);

    /**
     * @param array|callable|FactoryInterface|string $factory
     * @return callable|FactoryInterface
     */
    public function factory($factory);

    /**
     * @param string $name
     * @param mixed $options
     * @param bool $shared
     * @return false|object
     */
    public function get($name, $options = null, $shared = true);
}
