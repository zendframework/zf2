<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\Framework\View\Config as ViewConfig;

class ListenerFactory
    extends FactoryListener
{
    /**
     * @param EventInterface $event
     * @return Listener
     */
    public function __invoke(EventInterface $event)
    {
        $config = $this->sm->applicationConfig()['view_manager'];

        $vm = new Listener(new ViewConfig($config));
        $vm->setServiceManager($this->sm);

        return $vm;
    }
}
