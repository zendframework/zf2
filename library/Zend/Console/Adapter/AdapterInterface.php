<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Console\Adapter;

use Zend\Console\Charset\CharsetInterface;

interface AdapterInterface
{
    const LINE_NONE = 1;
    const LINE_SINGLE = 2;
    const LINE_DOUBLE = 3;
    const LINE_BLOCK = 4;
    const FILL_NONE = 0;
    const FILL_SHADE_LIGHT = 1;
    const FILL_SHADE_MEDIUM = 2;
    const FILL_SHADE_DARK = 3;
    const FILL_BLOCK = 10;

    /**
     * Write a chunk of text to console.
     *
     * @param string                   $text
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function write($text, $color = null, $bgColor = null);

    /**
     * Alias for write()
     *
     * @param string                   $text
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function writeText($text, $color = null, $bgColor = null);

    /**
     * Write a single line of text to console and advance cursor to the next line.
     * If the text is longer than console width it will be truncated.
     *
     * @param string                   $text
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function writeLine($text = "", $color = null, $bgColor = null);

    /**
     * Write a piece of text at the coordinates of $x and $y
     *
     * @param string                   $text     Text to write
     * @param int                      $x        Console X coordinate (column)
     * @param int                      $y        Console Y coordinate (row)
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function writeAt($text, $x, $y, $color = null, $bgColor = null);

    /**
     * Write a box at the specified coordinates.
     * If X or Y coordinate value is negative, it will be calculated as the distance from far right or bottom edge
     * of the console (respectively).
     *
     * @param int                      $x1           Top-left corner X coordinate (column)
     * @param int                      $y1           Top-left corner Y coordinate (row)
     * @param int                      $x2           Bottom-right corner X coordinate (column)
     * @param int                      $y2           Bottom-right corner Y coordinate (row)
     * @param int                      $lineStyle    (optional) Box border style.
     * @param int                      $fillStyle    (optional) Box fill style or a single character to fill it with.
     * @param int      $color        (optional) Foreground color
     * @param int      $bgColor      (optional) Background color
     * @param null|int $fillColor    (optional) Foreground color of box fill
     * @param null|int $fillBgColor  (optional) Background color of box fill
     */
    public function writeBox(
        $x1,
        $y1,
        $x2,
        $y2,
        $lineStyle = self::LINE_SINGLE,
        $fillStyle = self::FILL_NONE,
        $color = null,
        $bgColor = null,
        $fillColor = null,
        $fillBgColor = null
    );

    /**
     * Write a block of text at the given coordinates, matching the supplied width and height.
     * In case a line of text does not fit desired width, it will be wrapped to the next line.
     * In case the whole text does not fit in desired height, it will be truncated.
     *
     * @param string                   $text     Text to write
     * @param int                      $width    Maximum block width. Negative value means distance from right edge.
     * @param int|null                 $height   Maximum block height. Negative value means distance from bottom edge.
     * @param int                      $x        Block X coordinate (column)
     * @param int                      $y        Block Y coordinate (row)
     * @param null|int                 $color    (optional) Text color
     * @param null|int $bgColor  (optional) Text background color
     */
    public function writeTextBlock(
        $text,
        $width,
        $height = null,
        $x = 0,
        $y = 0,
        $color = null,
        $bgColor = null
    );


    /**
     * Determine and return current console width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Determine and return current console height.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Determine and return current console width and height.
     *
     * @return array        array($width, $height)
     */
    public function getSize();

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8();


//    /**
//     * Return current cursor position - array($x, $y)
//     *
//     * @return array        array($x, $y);
//     */
//    public function getPos();
//
//    /**
//     * Return current cursor X coordinate (column)
//     *
//     * @return  false|int       Integer or false if failed to determine.
//     */
//    public function getX();
//
//    /**
//     * Return current cursor Y coordinate (row)
//     *
//     * @return  false|int       Integer or false if failed to determine.
//     */
//    public function getY();

    /**
     * Set cursor position
     *
     * @param int   $x
     * @param int   $y
     */
    public function setPos($x, $y);

    /**
     * Hide console cursor
     */
    public function hideCursor();

    /**
     * Show console cursor
     */
    public function showCursor();

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set console window title
     *
     * @param $title
     */
    public function setTitle($title);

    /**
     * Reset console window title to previous value.
     */
    public function resetTitle();


    /**
     * Prepare a string that will be rendered in color.
     *
     * @param string                     $string
     * @param null|int   $color    Foreground color
     * @param null|int   $bgColor  Background color
     */
    public function colorize($string, $color = null, $bgColor = null);

    /**
     * Change current drawing color.
     *
     * @param int $color
     */
    public function setColor($color);

    /**
     * Change current drawing background color
     *
     * @param int $color
     */
    public function setBgColor($color);

    /**
     * Reset color to console default.
     */
    public function resetColor();


    /**
     * Set Console charset to use.
     *
     * @param CharsetInterface $charset
     */
    public function setCharset(CharsetInterface $charset);

    /**
     * Get charset currently in use by this adapter.
     *
     * @return CharsetInterface $charset
     */
    public function getCharset();

    /**
     * @return CharsetInterface
     */
    public function getDefaultCharset();

    /**
     * Clear console screen
     */
    public function clear();

    /**
     * Clear line at cursor position
     */
    public function clearLine();

    /**
     * Clear console screen
     */
    public function clearScreen();

    /**
     * Read a single line from the console input
     *
     * @param int $maxLength        Maximum response length
     * @return string
     */
    public function readLine($maxLength = 2048);

    /**
     * Read a single character from the console input
     *
     * @param string|null   $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null);
}
