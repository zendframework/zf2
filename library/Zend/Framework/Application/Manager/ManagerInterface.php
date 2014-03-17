<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Manager;

use Zend\Framework\Application\EventInterface as Application;
use Zend\Framework\Service\Manager\ManagerInterface as ServiceManagerInterface;

interface ManagerInterface
    extends ServiceManagerInterface
{
    /**
     * @param string $event
     * @param null $options
     * @return mixed
     */
    public function run($event = Application::EVENT, $options = null);
}
