<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\Application;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class ApplicationFactory
{
    /**
     * @param ServiceManager $sm
     * @return Application
     */
    public function createService(ServiceManager $sm)
    {
        return new Application($sm);
    }
}
