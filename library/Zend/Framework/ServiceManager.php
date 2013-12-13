<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ServiceManager\ConfigInterface as Config;

use Zend\Framework\ApplictionServiceTrait;

class ServiceManager
    extends ServiceManager\ServiceManager
{
    /**
     *
     */
    use ApplicationServiceTrait;

    /**
     * @var $this
     */
    protected $sm;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->sm = $this;
    }
}
