<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Event\ListenerTrait as Listener;
use Zend\Framework\Service\ServiceTrait as Service;

trait ListenerTrait
{
    /**
     *
     */
    use Listener,
        Service;

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($name);
    }

    /**
     * @param $name
     * @return array|object
     */
    public function controller($name)
    {
        return $this->sm->service($name);
    }
}
