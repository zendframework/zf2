<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model\Render;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\View\Model\ModelInterface as ViewModel;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @param string|ViewModel $viewModel
     */
    public function __construct($viewModel)
    {
        $this->source = $viewModel;
    }

    /**
     * @param callable $listener
     * @return mixed
     */
    public function __invoke(callable $listener)
    {
        return $listener($this, $this->source);
    }
}
