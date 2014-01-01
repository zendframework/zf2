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
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\Http\PhpEnvironment\Request as HttpRequest;

class Factory
    extends FactoryListener
{
    /**
     * @param EventInterface $event
     * @return HttpRequest|ConsoleRequest
     */
    public function __invoke(EventInterface $event)
    {
        if (Console::isConsole()) {
            return new ConsoleRequest();
        }

        return new HttpRequest();
    }
}
