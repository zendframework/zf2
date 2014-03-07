<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

trait ServicesTrait
{
    /**
     * @return ManagerInterface
     */
    public function viewPluginManager()
    {
        return $this->sm->get('View\Plugin\Manager');
    }

    /**
     * @param ManagerInterface $vm
     * @return self
     */
    public function setViewPluginManager(ManagerInterface $vm)
    {
        return $this->sm->add('View\Plugin\Manager', $vm);
    }
}
