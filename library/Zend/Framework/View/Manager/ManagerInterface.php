<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Exception;
use Zend\View\Model\ModelInterface as ViewModel;

interface ManagerInterface
{
    /**
     * @param Exception $exception
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function exception(Exception $exception, ViewModel $viewModel);

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function plugin($name, $options = null);

    /**
     * @param ViewModel $viewModel
     * @param null $options
     * @return mixed
     */
    public function render(ViewModel $viewModel, $options = null);

    /**
     * @param ViewModel $viewModel
     * @param null $options
     * @return mixed
     */
    public function renderer(ViewModel $viewModel, $options = null);

    /**
     * @param mixed $source
     * @param null $options
     * @return ViewModel
     */
    public function viewModel($source, $options = null);
}
