<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Resolver\Factory;

use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\View\Resolver\TemplateMapResolver;

class MapFactory
    extends Factory
{
    /**
     * @param Request $request
     * @param array $options
     * @return TemplateMapResolver
     */
    public function __invoke(Request $request, array $options = [])
    {
        return new TemplateMapResolver($this->config()->view()->templateMap());
    }
}
