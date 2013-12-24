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
     * @param $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
