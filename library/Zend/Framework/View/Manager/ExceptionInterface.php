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

interface ExceptionInterface
{
    /**
     * @param Exception $exception
     * @param ViewModel $viewModel
     * @return mixed
     */
    public function exception(Exception $exception, ViewModel $viewModel);
}
