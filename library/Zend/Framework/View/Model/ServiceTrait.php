<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\View\Model\ModelInterface as ViewModel;

trait ServiceTrait
{
    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function addChildViewModel($viewModel)
    {
        $this->viewModel->addChild($viewModel);
        return $this;
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    /**
     * @return ViewModel
     */
    public function viewModel()
    {
        return $this->viewModel;
    }
}
