<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Zend\Framework\Event\Manager\ConfigInterface as Config;
use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface;

interface ManagerInterface
    extends ListenerInterface
{
    /**
     * @param Config $listeners
     * @return self
     */
    public function config(Config $listeners);

    /**
     * @return Config
     */
    public function configuration();

    /**
     * @param string|EventInterface $event
     * @param $options
     * @return mixed
     */
    public function trigger($event, $options = null);
}
