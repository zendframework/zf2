<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\MvcEvent;
use Zend\Mvc\ResponseSender\ConsoleResponseSender;
use Zend\Mvc\ResponseSender\HttpResponseSender;
use Zend\Mvc\ResponseSender\PhpEnvironmentResponseSender;
use Zend\Mvc\ResponseSender\SimpleStreamResponseSender;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends EventListener
    implements FactoryInterface
{
    protected $name = 'mvc.response';

    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    public function __invoke(Event $event)
    {
        var_dump('>> '.__FILE__);
    }
}
