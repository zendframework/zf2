<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\View\ServicesTrait as View;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;

class Factory
    extends ServiceFactory
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
        $vm = new ViewModel;

        $vm->setTemplate($this->viewManager()->layoutTemplate());

        return $vm;
    }
}
