<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Model;

use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\View\Model\ModelInterface as ViewModel;

trait ServicesTrait
{
    /**
     *
     */
    use EventManager;

    /**
     * @return ViewModel
     */
    public function rootViewModel()
    {
        return $this->sm->get('View\Model');
    }

    /**
     * @param ViewModel $viewModel
     * @return self
     */
    public function setRootViewModel(ViewModel $viewModel)
    {
        return $this->sm->add('View\Model', $viewModel);
    }

    /**
     * @param null $options
     * @return ViewModel
     */
    public function viewModel($options = null)
    {
        return $this->trigger([Event::EVENT, $this], $options);
    }
}