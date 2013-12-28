<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\Mvc\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Mvc\Service\ListenerInterface as ServiceManager;

class Factory
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return ViewModel
     */
    public function createService(ServiceManager $sm)
    {
        $vm = new ViewModel;

        $vm->setTemplate($sm->getViewConfig()->getLayoutTemplate());

        return $vm;
    }
}
