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
     * @var string
     */
    protected $eventName = 'mvc.application';
}
