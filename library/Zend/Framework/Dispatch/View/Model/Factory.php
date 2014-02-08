<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch\View\Model;

use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\View\Model\ViewModel;
use Zend\Framework\View\ServicesTrait as View;

class Factory
    extends ServiceFactory
{
    /**
     *
     */
    use EventManager,
        Route,
        View;

    /**
     * @param Request $request
     * @param array $options
     * @return ViewModel
     */
    public function service(Request $request, array $options = [])
    {
        return $this->eventManager()->trigger(['Dispatch\View\Model\Event', [new ViewModel]], $options);
    }
}
