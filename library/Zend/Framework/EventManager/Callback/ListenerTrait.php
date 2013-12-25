<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\EventManager\Callback;

use Zend\Framework\EventManager\ListenerTrait as ListenerService;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param $callback
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($callback, $event = null, $target = null, $priority = null)
    {
        $this->setCallback($callback);

        parent::__construct($event, $target, $priority);
    }

    /**
     * @return callable
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * @param $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        return call_user_func($this->callback, $event);
    }
}
