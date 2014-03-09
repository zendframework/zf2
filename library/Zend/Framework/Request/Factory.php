<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Request;

use Zend\Console\Console;
use Zend\Console\Request as ConsoleRequest;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Http\PhpEnvironment\Request as HttpRequest;

class Factory
    extends ServiceFactory
{
    /**
     * @param Request $request
     * @param array $options
     * @return HttpRequest|ConsoleRequest
     */
    public function __invoke(Request $request, array $options = [])
    {
        if (Console::isConsole()) {
            return new ConsoleRequest();
        }

        return new HttpRequest();
    }
}
