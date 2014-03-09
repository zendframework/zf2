<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Service\Factory\Factory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Mvc\Router\Exception;

class PartFactory
    extends Factory
{
    /**
     * @param Request $request
     * @param array $options
     * @return mixed|void|Part
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(Request $request, array $options = [])
    {
        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['route_plugins'])) {
            throw new Exception\InvalidArgumentException('Missing "route_plugins" in options array');
        }

        if (!isset($options['prototypes'])) {
            $options['prototypes'] = null;
        }

        if (!isset($options['may_terminate'])) {
            $options['may_terminate'] = false;
        }

        if (!isset($options['child_routes']) || !$options['child_routes']) {
            $options['child_routes'] = null;
        }

        return new Part(
            $options['route'],
            $options['may_terminate'],
            $options['route_plugins'],
            $options['child_routes'],
            $options['prototypes']
        );
    }
}
