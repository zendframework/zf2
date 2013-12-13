<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\Framework\ApplicationServiceTrait;
use Zend\Framework\ServiceManager as ApplicationServiceManager;

use Zend\Framework\EventManager\Event;

class MvcEvent
    extends Event
{
    /**
     *
     */
    use ApplicationServiceTrait;

    /**
     *
     */
    const EVENT_NAME                = 'mvc.application';
    const EVENT_BOOTSTRAP           = 'mvc.bootstrap';
    const EVENT_DISPATCH            = 'mvc.dispatch';
    const EVENT_CONTROLLER_DISPATCH = 'mvc.controller.dispatch';
    const EVENT_DISPATCH_ERROR      = 'mvc.dispatch.error';
    const EVENT_RESPONSE            = 'mvc.response';
    const EVENT_RENDER              = 'mvc.render';
    const EVENT_RENDER_ERROR        = 'mvc.render.error';
    const EVENT_ROUTE               = 'mvc.route';

    /**
     * @var string
     */
    protected $name = self::EVENT_NAME;

    /**
     * @var ApplicationServiceManager
     */
    protected $sm;

    /**
     * @param ServiceManager $sm
     * @return $this
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @return ApplicationServiceManager
     */
    public function getServiceManager()
    {
        return $this->sm;
    }
}
