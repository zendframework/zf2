<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Response;

use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Framework\EventManager\Manager\ServiceTrait as EventManager;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager,
        Listener;
}
