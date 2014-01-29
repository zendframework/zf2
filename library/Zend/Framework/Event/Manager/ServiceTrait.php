<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $em;

    /**
     * @return ManagerInterface
     */
    public function eventManager()
    {
        return $this->em;
    }

    /**
     * @param ManagerInterface $em
     * @return self
     */
    public function setEventManager(ManagerInterface $em)
    {
        $this->em = $em;
        return $this;
    }
}
