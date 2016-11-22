<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Exception;

use Zend\Framework\View\Model\ServiceTrait as ViewModelTrait;
use Zend\View\Model\ModelInterface as ViewModel;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ViewModelTrait;

    /**
     * @param EventInterface $event
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function __invoke(EventInterface $event, ViewModel $viewModel)
    {
        return $viewModel->clearChildren()
                         ->addChild($this->viewModel()->setVariables(['exception' => $event->exception()]));
    }
}
