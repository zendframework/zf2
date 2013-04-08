<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

class NoCache extends AbstractAdapter
{
    /**
     * Internal method to get an item.
     *
     * @param  string  $normalizedKey
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     */
    protected function internalGetItem(& $normalizedKey, & $success = null,
        & $casToken = null
    ) {
        return null;
    }

    /**
     * Internal method to store an item.
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @return bool
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        return true;
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     * @return bool
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        return true;
    }
}