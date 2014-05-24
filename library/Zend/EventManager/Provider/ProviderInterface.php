<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager\Provider;

interface ProviderInterface
{
    /**
     * @param $eventName
     * @param $target
     * @param array $parameters
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName, $target = null, $parameters = array());
}