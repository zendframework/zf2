<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Console\Console;
use Zend\Console\Response as ConsoleResponse;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\Service\RequestInterface as Request;

class Factory
    extends ServiceFactory
{
    /**
     * @param Request $request
     * @param array $options
     * @return ConsoleResponse|HttpResponse
     */
    public function __invoke(Request $request, array $options = [])
    {
        if (Console::isConsole()) {
            return new ConsoleResponse();
        }

        return new HttpResponse();
    }
}
