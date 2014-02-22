<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $pm;

    /**
     * @return ManagerInterface
     */
    public function viewPluginManager()
    {
        return $this->pm;
    }

    /**
     * @param ManagerInterface $pm
     * @return self
     */
    public function setViewPluginManager(ManagerInterface $pm)
    {
        $this->pm = $pm;
        return $this;
    }
}
