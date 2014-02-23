<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Config;

use Serializable;

interface ConfigInterface
    extends Serializable
{
    /**
     * @param string $name
     * @param mixed $config
     * @return self
     */
    public function add($name, $config);

    /**
     * @param $name
     * @return mixed
     */
    public function config($name);

    /**
     * @return array
     */
    public function configuration();

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param $name
     * @return bool
     */
    public function has($name);
}
