<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Console\Console;
use Zend\Console\Response as ConsoleResponse;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Framework\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;

class Factory implements FactoryInterface
{
    /**
     * Create and return a response instance, according to current environment.
     *
     * @param  ServiceManager $sm
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceManager $sm)
    {
        if (Console::isConsole()) {
            return new ConsoleResponse();
        }

        return new HttpResponse();
    }
}
