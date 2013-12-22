<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager as ApplicationServiceManager;

trait ApplicationServiceManagerTrait
{

    /**
     * @var ApplicationServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @return ApplicationServiceManager
     */
    public function getServiceManager()
    {
        return $this->sm;
    }
}
