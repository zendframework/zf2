<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Service\ManagerInterface as ServiceManager;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param array $config
     * @param ServiceManager $sm
     */
    public function __construct(array $config, ServiceManager $sm)
    {
        $this->sm     = $sm;
        $this->config = $config;
        $this->alias  = $config['route_plugins'];
    }
}
