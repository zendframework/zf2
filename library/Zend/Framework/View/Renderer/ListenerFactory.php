<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\View\ServicesTrait as View;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use View;

    /**
     * @param Request $request
     * @return Renderer
     */
    public function service(Request $request)
    {
        $listener = new Listener;

        $listener->setViewRenderer($this->viewRenderer());

        return $listener;
    }
}
