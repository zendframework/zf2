<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use Traversable;

/**
 * Interface to automate setter injection for a SharedEventManagerInterface instance
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
     * Set the identifiers (overrides any currently set identifiers)
     *
     * @param  array|Traversable $identifiers
     * @return void
     */
    public function setIdentifiers($identifiers);

    /**
     * Add some identifier(s) (appends to any currently set identifiers)
     *
     * @param  array|Traversable $identifiers
     * @return void
     */
    public function addIdentifiers($identifiers);

    /**
     * Get the identifier(s) for this EventManager
     *
     * @return array
     */
    public function getIdentifiers();
}
