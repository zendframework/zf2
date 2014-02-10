<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Zend\Framework\Event\Manager\ConfigInterface as Config;
use Zend\Framework\Event\EventInterface;

interface ManagerInterface
{
    /**
     * @return Config
     */
    public function listeners();

    /**
     * @param string|EventInterface $event
     * @param $options
     * @return mixed
     */
    public function trigger($event, $options = null);
}
