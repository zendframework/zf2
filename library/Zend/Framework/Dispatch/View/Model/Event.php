<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch\View\Model;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\Event\ListenerInterface;
use Zend\View\Model\ModelInterface as ViewModel;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @var string
     */
    protected $name = self::EVENT_DISPATCH_VIEW_MODEL;

    /**
     * @param ViewModel $viewModel
     */
    public function __construct(ViewModel $viewModel)
    {
        $this->source = $viewModel;
    }

    /**
     * @param ListenerInterface $listener
     * @param $options
     * @return mixed
     */
    public function trigger(ListenerInterface $listener, $options = null)
    {
        return $listener->trigger($this, $options);
    }
}
