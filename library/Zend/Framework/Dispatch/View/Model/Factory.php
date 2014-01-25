<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch\View\Model;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\ServicesTrait as View;

class Factory
    extends FactoryListener
{
    /**
     *
     */
    use View;

    /**
     * @param Request $request
     * @return ViewModel
     */
    public function service(Request $request)
    {
        $em = $this->sm->get('EventManager');

        $vm = new ViewModel;

        $vm->setTemplate($this->viewManager()->layoutTemplate());

        return $em->trigger(new Event, $vm);
    }
}
