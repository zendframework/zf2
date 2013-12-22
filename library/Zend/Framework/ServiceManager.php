<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ApplictionServiceTrait;
use Zend\Framework\ServiceManager\ConfigInterface as Config;

class ServiceManager
    extends ServiceManager\ServiceManager
{
    /**
     *
     */
    use ApplicationServiceTrait;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->sm = $this;
    }

    /**
     * @param $name
     * @return object
     */
    public function getService($name)
    {
        return parent::getService($name);
    }
}
