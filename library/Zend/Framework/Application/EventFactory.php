<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class EventFactory
    extends Factory
{
    /**
     *
     */
    use EventManager;

    /**
     * @param Request $request
     * @param array $listeners
     * @return Event
     */
    public function __invoke(Request $request, array $listeners = [])
    {
        return new Event($this->sm);
    }
}
