<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Service;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\ListenerTrait as Listener;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager,
        Config,
        Listener;
}
