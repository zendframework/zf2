<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer\Service;

use Zend\Framework\Service\EventInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\View\Renderer\RendererInterface;

class ListenerFactory
    extends Factory
{
    /**
     *
     */
    use ServicesTrait;

    /**
     * @param Request $request
     * @param array $options
     * @return RendererInterface
     */
    public function __invoke(Request $request, array $options = [])
    {
        return (new Listener)->setViewRenderer($this->viewRenderer());
    }
}
