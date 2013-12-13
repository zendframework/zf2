<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\ServiceListenerInterface as ServiceListener;

interface ServiceRequestInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return bool
     */
    public function isShared();

    /**
     * @return string|array
     */
    public function getTarget();

    /**
     * @param string|array $target
     * @return mixed
     */
    public function setTarget($target);

    /**
     * @return array
     */
    public function getListeners();

    /**
     * @param ServiceListener $listener
     * @return mixed
     */
    public function __invoke(ServiceListener $listener);
}
