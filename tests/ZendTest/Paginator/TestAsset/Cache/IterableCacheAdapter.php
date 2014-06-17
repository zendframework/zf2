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
 * @package ZendTest\Paginator\TestAsset\Cache
 */
class IterableCacheAdapter extends BasicCacheAdapter implements IterableInterface
{
    public function getIterator()
    {
        return new KeyListIterator($this, array_keys($this->data));
    }
}
