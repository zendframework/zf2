<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

interface SynchronizerInterface extends \SplSubject
{
    /**
     * Set a service to synchronize
     * 
     * @param $name
     * @param $service
     * @return self
     */
    public function synchronize($name, $service);

    /**
     * @return array of services to synchronize
     */
    public function toSynchronize();
}
