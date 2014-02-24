<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Config\ConfigInterface as Config;

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $sm;

    /**
     * @return Config
     */
    public function config()
    {
        return $this->sm->config();
    }

    /**
     * @return ManagerInterface
     */
    public function serviceManager()
    {
        return $this->sm;
    }

    /**
     * @return ConfigInterface
     */
    public function services()
    {
        return $this->config()->services();
    }

    /**
     * @param ManagerInterface $sm
     * @return self
     */
    public function setServiceManager(ManagerInterface $sm)
    {
        $sm->add('ServiceManager', $this->sm = $sm);
        return $this;
    }
}