<?php

namespace ZendTest\Code\Reflection\TestAsset;

/**
 * TestSampleInterface1 DocBlock Short Desc
 *
 * This is a long description for
 * the docblock of this class, it
 * should be longer than 3 lines.
 * It indeed is longer than 3 lines
 * now.
 *
 * @author daanbiesterbos@gmail.com
 * @method test()
 * @property $test
 */
interface TestSampleInterface1
{
    /**
     * Method ShortDescription
     *
     * Method LongDescription
     * This is a long description for
     * the docblock of this class, it
     * should be longer than 3 lines.
     * It indeed is longer than 3 lines
     * now.
     *
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return mixed Some return descr
     */
    public function doSomething($one, $two = 2, $three = 'three', array $array = array(), $class = null);

    /**
     * Method ShortDescription
     *
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return int
     */
    public function doSomethingElse($one, $two = 2, $three = 'three');

    /**
     * Method ShortDescription
     *
     * Method LongDescription
     * This is a long description for
     * the docblock of this class, it
     * should be longer than 3 lines.
     * It indeed is longer than 3 lines
     * now.
     *
     * @param int $one Description for one
     * @param int Description for two
     * @param string $three Description for three
     *                      which spans multiple lines
     * @return mixed Some return descr
     */
    public function orDoNothingAtAll($one, $two = 2, $three = 'three', array $array = array(), $class = null);
}

