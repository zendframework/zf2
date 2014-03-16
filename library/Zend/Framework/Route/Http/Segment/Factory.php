<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http\Segment;

use Zend\Framework\Service\Factory\Factory as FactoryService;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Mvc\Router\Exception;

class Factory
    extends FactoryService
{
    /**
     * @param Request $request
     * @param array $options
     * @return Segment
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(Request $request, array $options = [])
    {
        return new Segment(
            $options['name'],
            $options['options']['route'],
            $options['options']['constraints'],
            $options['options']['defaults']
        );
    }
}
