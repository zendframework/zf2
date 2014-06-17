<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator\TestAsset\Cache;

use Zend\Cache\Storage\Adapter\AbstractAdapter;

/**
 * Class BasicCacheAdapter
 *
 * A minimal cache adapter
 * Used to test cache with Paginator.
 *
 * @package ZendTest\Paginator\TestAsset\Cache
 */
class BasicCacheAdapter extends AbstractAdapter
{
    protected $data = array();

    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        if (isset($this->data[$normalizedKey])) {
            return $this->data[$normalizedKey];
        }
        return null;
    }

    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $this->data[$normalizedKey] = $value;
        return true;
    }

    protected function internalRemoveItem(& $normalizedKey)
    {
        if (isset($this->data[$normalizedKey])) {
            unset($this->data[$normalizedKey]);
        }
        return true;
    }
}
