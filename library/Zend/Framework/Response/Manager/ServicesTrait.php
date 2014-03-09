<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response\Manager;

use Zend\Mvc\Router\RouteMatch;
use Zend\Framework\Route\RouteInterface as Router;

trait ServicesTrait
{
    /**
     * @return ManagerInterface
     */
    public function responseManager()
    {
        return $this->sm->get('Response\Manager');
    }

    /**
     * @param ManagerInterface $rm
     * @return self
     */
    public function setResponseManager(ManagerInterface $rm)
    {
        $this->sm->add('Response\Manager', $rm);
        return $this;
    }
}
