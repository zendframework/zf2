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

class LiteralFactory
    extends Factory
{
    /**
     * @param Request $request
     * @param array $options
     * @return Literal
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(Request $request, array $options = [])
    {
        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new Literal($options['route'], $options['defaults']);
    }
}
