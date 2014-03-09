<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\View\Model\ModelInterface as ViewModel;

trait ServicesTrait
{
    /**
     * @param \Exception $exception
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function exception(\Exception $exception, ViewModel $viewModel)
    {
        return $this->viewManager()->exception($exception, $viewModel);
    }

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function plugin($name, $options = null)
    {
        return $this->viewManager()->plugin($name, $options);
    }

    /**
     * @param Event|string $event
     * @param null $options
     * @return mixed
     */
    public function render($event, $options = null)
    {
        return $this->viewManager()->render($event, $options);
    }

    /**
     * @param ManagerInterface $vm
     * @return self
     */
    public function setViewManager(ManagerInterface $vm)
    {
        $this->sm->set('View\Manager', $vm);
        return $this;
    }

    /**
     * @return ManagerInterface
     */
    public function viewManager()
    {
        return $this->sm->get('View\Manager');
    }
}