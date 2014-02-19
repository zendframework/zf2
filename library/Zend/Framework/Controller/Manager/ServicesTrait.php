<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Event\Manager\ConfigInterface;

trait ServicesTrait
{
    /**
     * @return ManagerInterface
     */
    public function controllerManager()
    {
        return $this->sm->get('Controller\Manager');
    }

    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function dispatch($event, $options = null)
    {
        return $this->controllerManager()->dispatch($event, $options);
    }

    /**
     * @param $name
     * @return bool
     */
    public function dispatchable($name)
    {
        return $this->controllerManager()->listeners()->has($name);
    }

    /**
     * @param ManagerInterface $cm
     * @return self
     */
    public function setControllerManager(ManagerInterface $cm)
    {
        $this->sm->add('Controller\Manager', $cm);
        return $this;
    }
}