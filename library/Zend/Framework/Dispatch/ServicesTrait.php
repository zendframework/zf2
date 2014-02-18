<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

trait ServicesTrait
{
    /**
     * @param $name
     * @param null $options
     * @return callable|ListenerInterface
     */
    public function controller($name, $options = null)
    {
        return $this->sm->get($name, $options, false);
    }

    /**
     * @param $name
     * @param callable|ListenerInterface $controller
     * @return self
     */
    public function setController($name, callable $controller)
    {
        $this->sm->get($name, $controller);
        return $this;
    }
}