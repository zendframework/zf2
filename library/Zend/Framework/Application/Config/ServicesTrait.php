<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Config;

trait ServicesTrait
{
    /**
     * @return array
     */
    public function applicationConfig()
    {
        return $this->service('ApplicationConfig');
    }

    /**
     * @param array $config
     * @return self
     */
    public function setApplicationConfig(array $config)
    {
        return $this->add('ApplicationConfig', $config);
    }
}
