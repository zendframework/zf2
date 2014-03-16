<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Assemble;

use Zend\Framework\Route\Manager\ManagerInterface as RouteManager;

trait ServicesTrait
{
    /**
     * @param RouteManager $rm
     * @return AssemblerInterface
     */
    public function assembler(RouteManager $rm)
    {
        return $this->sm->get('Route\Assembler', $rm);
    }
}
