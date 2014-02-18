<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

trait ServiceTrait
{
    /**
     * @var callable|ListenerInterface
     */
    protected $controller;

    /**
     * @return callable|ListenerInterface
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @param callable|ListenerInterface $controller
     * @return self
     */
    public function setController(callable $controller)
    {
        $this->controller = $controller;
        return $this;
    }
}