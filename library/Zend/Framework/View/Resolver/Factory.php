<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\View\Resolver\AggregateResolver as ViewResolver;

class Factory
    extends FactoryListener
{
    /**
     * @param Request $request
     * @return ViewResolver
     */
    public function service(Request $request)
    {
        $resolver = new ViewResolver;
        $resolver->attach($this->sm->get('View\Template\Resolver\Map'));
        $resolver->attach($this->sm->get('View\Template\Resolver\PathStack'));
        return $resolver;
    }
}
