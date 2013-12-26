<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Framework\Application\ServiceTrait as Services;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\View\Renderer\RendererInterface as Renderer;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService, Services;
}
