<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace Zend\EventManager;

/**
 * Interface to automate setter injection for a SharedEventManagerInterface instance
 *
 * @category   Zend
 * @package    Zend_EventManager
 */
interface SharedEventManagerAwareInterface
{
    /**
     * Inject a SharedEventManager instance
     *
     * @param  SharedEventManagerInterface $sharedEventManager
     * @return SharedEventManagerAwareInterface
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager);

    /**
     * Get shared collections container
     *
     * @return SharedEventManagerInterface
     */
    public function getSharedManager();

    /**
     * Remove any shared collections
     *
     * @return void
     */
    public function unsetSharedManager();
}
