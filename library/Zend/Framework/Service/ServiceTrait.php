<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

trait ServiceTrait
{
    /**
     * @var ListenerInterface
     */
    protected $sm;

    /**
     * @return ListenerInterface
     */
    public function serviceManager()
    {
        return $this->sm;
    }

    /**
     * @param ListenerInterface $sm
     * @return self
     */
    public function setServiceManager(ListenerInterface $sm)
    {
        $this->sm = $sm;

        $this->sm->add('ServiceManager', $sm);

        return $this;
    }
}