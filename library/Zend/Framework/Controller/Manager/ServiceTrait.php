<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

trait ServiceTrait
{
    /**
     * @var ListenerInterface
     */
    protected $cm;

    /**
     * @return ListenerInterface
     */
    public function controllerManager()
    {
        return $this->cm;
    }

    /**
     * @param ListenerInterface $cm
     * @return self
     */
    public function setControllerManager(ListenerInterface $cm)
    {
        $this->cm = $cm;
        return $this;
    }
}
