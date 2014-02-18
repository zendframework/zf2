<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\View\Model;

use Zend\View\Model\ModelInterface as ViewModel;

trait ServiceTrait
{
    /**
     * @var ViewModel
     */
    protected $controllerViewModel;

    /**
     * @return ViewModel
     */
    public function controllerViewModel()
    {
        return $this->controllerViewModel;
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setControllerViewModel(ViewModel $viewModel)
    {
        $this->controllerViewModel = $viewModel;
        return $this;
    }
}
