<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager;

use Zend\Framework\EventManager\EventManagerInterface;
use Zend\Framework\EventManager\ListenerAggregateInterface;

/**
 * Abstract aggregate listener
 */
abstract class AbstractListenerAggregate
    implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $em)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($em->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }
}
