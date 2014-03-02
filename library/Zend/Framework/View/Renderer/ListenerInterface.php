<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\View\Model\ModelInterface as ViewModel;

interface ListenerInterface
{
    /**
     * @param EventInterface $event
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function __invoke(EventInterface $event, ViewModel $viewModel);
}
