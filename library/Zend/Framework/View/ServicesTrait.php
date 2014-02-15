<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

trait ServicesTrait
{
    /**
     * @return array
     */
    public function viewConfig()
    {
        return $this->viewManager()->viewConfig();
    }

    /**
     * @return ManagerInterface
     */
    public function viewManager()
    {
        return $this->sm->get('View\Manager', null, false);
    }

    /**
     * @param ManagerInterface $vm
     * @return self
     */
    public function setViewManager(ManagerInterface $vm)
    {
        return $this->sm->add('View\Manager', $vm);
    }
}
