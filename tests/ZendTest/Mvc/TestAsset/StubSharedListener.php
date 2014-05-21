<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\TestAsset;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ModuleManager\ModuleEvent;

class StubSharedListener implements SharedListenerAggregateInterface
{
    protected $listeners = array();

    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach('Zend\ModuleManager\ModuleManager', ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'), -1000);
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onMergeConfig(ModuleEvent $e)
    {
        $e->setParam('StubSharedListener', $this);
    }
}
