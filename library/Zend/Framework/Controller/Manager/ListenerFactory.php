<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\Factory\Listener as FactoryListener;

class ListenerFactory
    extends FactoryListener
{
    /**
     *
     */
    use Config;

    /**
     * @param EventInterface $event
     * @return void|Listener
     */
    public function service(EventInterface $event)
    {
        $config = $this->appConfig()['controllers'];

        $cm = new Listener($config);

        $cm->setServiceManager($this->sm);

        return $cm;
    }
}
