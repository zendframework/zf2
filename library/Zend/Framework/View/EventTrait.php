<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\EventManager\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\View\Renderer\ServicesTrait as ViewRenderer;
use Zend\View\Model\ModelInterface as ViewModel;

trait EventTrait
{
    /**
     *
     */
    use Event, EventManager, Services, ViewRenderer;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var ViewModel
     */
    protected $vm;

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return ViewModel
     */
    public function viewModel()
    {
        return $this->vm;
    }

    /**
     * @param ViewModel $vm
     * @return self
     */
    public function setViewModel(ViewModel $vm)
    {
        $this->vm = $vm;
    }
}
