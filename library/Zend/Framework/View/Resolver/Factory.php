<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver;

use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\View\Resolver\Factory\ServicesTrait as ResolverFactory;
use Zend\View\Resolver\AggregateResolver as ViewResolver;

class Factory
    extends ServiceFactory
{
    /**
     *
     */
    use ResolverFactory;

    /**
     * @param Request $request
     * @param array $options
     * @return ViewResolver
     */
    public function __invoke(Request $request, array $options = [])
    {
        return (new ViewResolver)->attach($this->map())->attach($this->pathStack());
    }
}
