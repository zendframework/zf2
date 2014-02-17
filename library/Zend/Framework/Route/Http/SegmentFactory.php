<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\Mvc\Router\Exception;

class SegmentFactory
    extends Factory
{
    /**
     * @param Request $request
     * @param array $options
     * @return Segment
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(Request $request, array $options = [])
    {
        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['constraints'])) {
            $options['constraints'] = array();
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new Segment($options['route'], $options['constraints'], $options['defaults']);
    }
}
