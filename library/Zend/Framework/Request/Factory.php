<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Request;

use Zend\Console\Console;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

class Factory implements FactoryInterface
{
    /**
     * Create and return a request instance, according to current environment.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ConsoleRequest|HttpRequest
     */
    public function createService(ServiceManager $serviceLocator)
    {
        if (Console::isConsole()) {
            return new ConsoleRequest();
        }

        return new HttpRequest();
    }
}
