<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\TestAsset;

use Zend\Cache\Storage\Adapter\AbstractAdapter;

class MockAdapter extends AbstractAdapter
{

    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
    }

    protected function internalSetItem(& $normalizedKey, & $value, $ttl = 0)
    {
    }

    protected function internalRemoveItem(& $normalizedKey)
    {
    }
}
