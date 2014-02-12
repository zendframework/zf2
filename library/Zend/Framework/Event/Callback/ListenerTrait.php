<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Callback;

use Zend\Framework\Event\ListenerTrait as EventListener;

trait ListenerTrait
{
    /**
     *
     */
    use EventListener;

    /**
     * Callback
     *
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param $callback
     */
    public function __construct($callback)
    {
        $this->setCallback($callback);
    }

    /**
     * Callback
     *
     * @return callable
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * Callback set
     *
     * @param $callback
     * @return self
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }
}
