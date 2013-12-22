<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ApplicationServiceTrait;
use Zend\Framework\EventManager\Event as EventClass;
use Zend\Framework\ServiceManager as ApplicationServiceManager;

class MvcEvent
    extends EventClass
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
    protected $eventName = self::EVENT_NAME;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * Currently only used by the Mvc Dispatch Listener, stores the results of the mvc.dispatch event
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}
