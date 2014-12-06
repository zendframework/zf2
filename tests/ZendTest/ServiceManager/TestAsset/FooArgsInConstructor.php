<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\ServiceManager\TestAsset;

class FooArgsInConstructor
{
    /**
     * @param \Foo $foo
     * @param string $string
     * @param int $int
     * @throws \InvalidArgumentException
     */
    public function __construct(Foo $foo, $string, $int)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException('Passed argument must be a string');
        }
        if (!is_int($int)) {
            throw new \InvalidArgumentException('Passed argument must be an integer');
        }
    }
}
