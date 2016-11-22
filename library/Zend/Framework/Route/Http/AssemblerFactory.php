<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Request\ServicesTrait as RequestTrait;
use Zend\Framework\Route\Assemble\ServicesTrait as RouteAssembler;
use Zend\Framework\Route\Manager\ServicesTrait as Route;
use Zend\Framework\Service\Factory\Factory as FactoryService;
use Zend\Framework\Service\RequestInterface as Request;

class AssemblerFactory
    extends FactoryService
{
    /**
     *
     */
    use RequestTrait;

    /**
     * @param Request $request
     * @param array $options
     * @return Assembler
     */
    public function __invoke(Request $request, array $options = [])
    {
        $routeManager = $options[0];

        $request = $this->request();

        return new Assembler(
            $routeManager,
            $request->getUri(),
            $request->getBaseUrl(),
            $this->config()->routes()->defaultParams()
        );
    }
}
