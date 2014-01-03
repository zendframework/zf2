<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;

class ListenerFactory
    extends FactoryListener
{
    /**
     * @param EventInterface $event
     * @return void|Listener
     */
    public function __invoke(EventInterface $event)
    {
        $config = $this->sm->applicationConfig();

        $cm = new Listener($config['controllers']);
        $cm->setServiceManager($this->sm);

        return $cm;
    }
}
