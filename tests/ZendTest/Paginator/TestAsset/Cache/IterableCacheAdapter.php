<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator\TestAsset\Cache;

use Zend\Cache\Storage\Adapter\KeyListIterator;
use Zend\Cache\Storage\IterableInterface;

/**
 * Class IterableCacheAdapter
 *
 * A minimal cache adapter based on Zend\Cache\Storage\Adapter\Memory
 * implementing only IterableInterface
 * Used to test cache with Paginator.
 *
 * @package ZendTest\Paginator\TestAsset\Cache
 */
class IterableCacheAdapter extends BasicCacheAdapter implements
    IterableInterface
{
    /* IterableInterface */

    /**
     * Get the storage iterator
     *
     * @return KeyListIterator
     */
    public function getIterator()
    {
        $ns   = $this->getOptions()->getNamespace();
        $keys = array();

        if (isset($this->data[$ns])) {
            foreach ($this->data[$ns] as $key => & $tmp) {
                if ($this->internalHasItem($key)) {
                    $keys[] = $key;
                }
            }
        }

        return new KeyListIterator($this, $keys);
    }
}
