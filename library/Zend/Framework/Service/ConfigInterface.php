<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Application\Config\ConfigInterface as ApplicationConfigInterface;
use Zend\Framework\Config\ConfigInterface as Config;

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
     * @param $name
     * @return mixed
     */
    public function assigned($name);

    /**
     * @return ApplicationConfigInterface
     */
    public function config();

    /**
     * @param $name
     * @return self
     */
    public function initialized($name);

    /**
     * @param $name
     * @return bool
     */
    public function initializing($name);

    /**
     * @param $name
     * @return object|null
     */
    public function service($name);
}
