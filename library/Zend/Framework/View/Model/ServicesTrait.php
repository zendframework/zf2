<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\View\Model\ModelInterface as ViewModel;

trait ServicesTrait
{
    /**
     * @return bool|ViewModel
     */
    public function viewModel()
    {
        return $this->service('View\Model');
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel)
    {
        return $this->add('View\Model', $viewModel);
    }
}
