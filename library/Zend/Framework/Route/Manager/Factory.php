<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Route\Assemble\ServicesTrait as RouteAssembler;
use Zend\Framework\Service\Factory\Factory as FactoryService;
use Zend\Framework\Service\RequestInterface as Request;

class Factory
    extends FactoryService
{
    /**
     *
     */
    use RouteAssembler;

    /**
     * @param Request $request
     * @param array $options
     * @return Manager
     */
    public function __invoke(Request $request, array $options = [])
    {
        $rm = new Manager($this->config());

        $rm->setAssembler($this->assembler($rm));

        return $rm;
    }
}
