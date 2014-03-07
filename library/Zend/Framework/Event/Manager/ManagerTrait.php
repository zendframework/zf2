<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Zend\Framework\Event\Config\ConfigInterface;
use Zend\Framework\Event\EventInterface;

trait ManagerTrait
{
    /**
     * @var ConfigInterface
     */
    protected $listeners;

    /**
     * @param array|EventInterface|string $event
     * @return EventInterface
     */
    abstract protected function event($event);

    /**
     * @return ConfigInterface
     */
    public function listeners()
    {
        return $this->listeners;
    }

    /**
     * @param array|EventInterface|string $event
     * @param null $options
     * @return mixed
     */
    public function trigger($event, $options = null)
    {
        return $this->__invoke($this->event($event), $options);
    }
}
