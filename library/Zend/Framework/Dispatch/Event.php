<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\ApplicationServiceTrait as ServiceTrait;
use Zend\Framework\EventManager\EventTrait as EventTrait;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait, ServiceTrait;

    /**
     * @var string
     */
    protected $eventName = self::EVENT_DISPATCH;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = self::WILDCARD;

    /**
     * @var bool Whether or not to stop propagation
     */
    protected $eventStopPropagation = false;

    /**
     * @var mixed
     */
    protected $result;

    /**
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
