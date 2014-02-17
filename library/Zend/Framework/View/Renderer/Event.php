<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

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
     * @param callable $listener
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function __invoke(callable $listener, ViewModel $viewModel)
    {
        return $listener($this, $viewModel);
    }
}
