<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;
use Zend\View\Helper as ViewHelper;

class ListenerFactory
    extends FactoryListener
{
    /**
     *
     */
    use Config;

    /**
     * @param EventInterface $event
     * @return Listener
     */
    public function __invoke(EventInterface $event)
    {
        $config = $this->applicationConfig()['router'];

        $vm = new Listener($config, $this->sm);

        return $vm;
    }
}
