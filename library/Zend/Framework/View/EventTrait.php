<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\EventManager\EventTrait as EventService;
use Zend\Framework\Application\ServiceTrait as Services;
use Zend\Framework\View\Model\ViewModel;

trait EventTrait
{
    /**
     *
     */
    use EventService, Services;

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
    public function getResult()
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
    public function getViewModel()
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
