<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Event\EventTrait as Event;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\Event\ResultTrait as Result;
use Zend\Framework\Service\ServiceTrait as Service;
use Zend\Framework\View\Renderer\ServiceTrait as ViewRenderer;

trait EventTrait
{
    /**
     *
     */
    use Event;
}
