<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Unserialize;

use Zend\Framework\Application\Config\ConfigInterface as Config;
use Zend\Framework\Service\ServiceTrait;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @param EventInterface $event
     * @param Config $config
     * @return mixed
     */
    public function __invoke(EventInterface $event, Config $config)
    {
        $this->sm->add('Config', $config)
                 ->add('EventManager', $this->sm);
    }
}
