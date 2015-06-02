<?php

namespace ZendTest\Code\Reflection\TestAsset;

/**
 * @author daanbiesterbos@gmail.com
 * @method test()
 * @property $test
 */
interface TestSampleInterface2
{
    /**
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return mixed Some return descr
     */
    public function doSomething($one, $two = 2, $three = 'three', array $array = array(), $class = null);

    /**
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return int
     */
    public function doSomethingElse($one, $two = 2, $three = 'three');
}

