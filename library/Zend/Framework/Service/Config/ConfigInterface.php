<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Config;

use Zend\Framework\Config\ConfigInterface as Config;
use Zend\Framework\Application\Manager\ConfigInterface as ApplicationConfig;

interface ConfigInterface
    extends Config
{
    /**
     * @param string $name
     * @param callable $factory
     * @return self
     */
    public function assign($name, callable $factory);

    /**
     * @param string $name
     * @return mixed
     */
    public function assigned($name);

    /**
     * @return ApplicationConfig
     */
    public function config();

    /**
     * @param string $name
     * @param mixed $config
     * @return self
     */
    public function configure($name, $config);

    /**
     * @param string $name
     * @return mixed
     */
    public function configured($name);

    /**
     * @param string $name
     * @return object
     */
    public function service($name);
}
