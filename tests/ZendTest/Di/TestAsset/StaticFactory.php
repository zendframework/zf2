<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Di\TestAsset;

class StaticFactory
{
    public static function factory(Struct $struct, array $params = [])
    {
        $params = array_merge((array) $struct, $params);
        return new DummyParams($params);
    }
}
