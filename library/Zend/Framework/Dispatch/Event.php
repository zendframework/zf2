<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        ViewModel;

    /**
     * @var string
     */
    protected $name = self::EVENT_NAME;

    /**
     * @param ListenerInterface $listener
     * @param $options
     * @return mixed
     */
    public function __invoke(ListenerInterface $listener, $options = null)
    {
        return $listener->__invoke($this, $options);
    }
}
