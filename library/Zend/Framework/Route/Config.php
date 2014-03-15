<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Event\Config\ConfigTrait as ConfigTrait;

class Config
    implements ConfigInterface
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @param string $name
     * @return Generator
     */
    public function queue($name)
    {
        if (!isset($this->config[$name])) {
            return;
        }

        foreach(array_keys($this->config[$name]) as $route) {
            yield $this->config[$name][$route];
        }
    }
}
