<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\Framework\View\Manager\Listener as ViewManager;

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
     * @return ViewManager
     */
    public function viewManager()
    {
        return $this->sm->get('View\Manager');
    }

    /**
     * @param ViewManager $vm
     * @return self
     */
    public function setViewManager(ViewManager $vm)
    {
        return $this->sm->add('View\Manager', $vm);
    }
}
