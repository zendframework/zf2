<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

interface ListenerConfigInterface
{
    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null);
    /**
     * @param string $name
     * @param mixed $config
     * @return self
     */
    public function add($name, $config);
}
