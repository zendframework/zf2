<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\ServiceManager\Request;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\View;

use Zend\Framework\ServiceManager\FactoryInterface;

class Factory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return View
     */
    public function createService(ServiceManager $sm)
    {
        $view = new View;
        $view->setEventManager($sm->getEventManager());
        return $view;
    }
}
