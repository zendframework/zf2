<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $vm;

    /**
     * @return ManagerInterface
     */
    public function viewManager()
    {
        return $this->vm;
    }

    /**
     * @param ManagerInterface $vm
     * @return self
     */
    public function setViewManager(ManagerInterface $vm)
    {
        $this->vm = $vm;
        return $this;
    }
}
