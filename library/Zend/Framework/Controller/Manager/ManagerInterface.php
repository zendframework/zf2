<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

interface ManagerInterface
{
    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function dispatch($event, $options = null);

    /**
     * @param string $controller
     * @return bool
     */
    public function dispatchable($controller);
}