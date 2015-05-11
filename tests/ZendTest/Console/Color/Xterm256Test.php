<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console\Color;

use ReflectionClass;
use Zend\Console\Color\Xterm256;

/**
 * @group      Zend_Console
 */
class Xterm256Test extends \PHPUnit_Framework_TestCase
{
    public function invalidHexCodes()
    {
        return [
            'too-long'                       => ['FFFFFF0'],
            'too-long-and-char-out-of-range' => ['ABCDEFG'],
            'too-long-digits'                => ['01048212'],
            'too-long-and-invalid-enc'       => ['ééààööüü'],
            'char-out-of-range'              => ['FF00GG'],
            'null'                           => [null],
        ];
    }

    /**
     * @dataProvider invalidHexCodes
     */
    public function testWrongHexCodeInputs($hex)
    {
        $color = Xterm256::calculate($hex);
        $r     = new ReflectionClass($color);
        $code  = $r->getStaticPropertyValue('color');
        $this->assertNull($code);
    }

    public function approximateHexCodes()
    {
        return [
            'sixteen'         => ['000100', 16],
            'one-ninety-nine' => ['FF33A0', 199],
        ];
    }

    /**
     * @dataProvider approximateHexCodes
     */
    public function testApproximateHexCodeInputs($hex, $gcode)
    {
        $color = Xterm256::calculate($hex);
        $r     = new ReflectionClass($color);
        $gcode = sprintf('%%s;5;%s', $gcode);
        $this->assertEquals($gcode, $r->getStaticPropertyValue('color'));
    }

    public function exactHexCodes()
    {
        return [
            '000000' => ['000000', 16],
            '00005F' => ['00005F', 17],
            '000087' => ['000087', 18],
            '0000AF' => ['0000AF', 19],
            '0000D7' => ['0000D7', 20],
            '0000FF' => ['0000FF', 21],
            '005F00' => ['005F00', 22],
            '005F5F' => ['005F5F', 23],
            '005F87' => ['005F87', 24],
            '005FAF' => ['005FAF', 25],
            '005FD7' => ['005FD7', 26],
            '005FFF' => ['005FFF', 27],
            '008700' => ['008700', 28],
            '00875F' => ['00875F', 29],
            '008787' => ['008787', 30],
            '0087AF' => ['0087AF', 31],
            '0087D7' => ['0087D7', 32],
            '0087FF' => ['0087FF', 33],
            '00AF00' => ['00AF00', 34],
            '00AF5F' => ['00AF5F', 35],
            '00AF87' => ['00AF87', 36],
            '00AFAF' => ['00AFAF', 37],
            '00AFD7' => ['00AFD7', 38],
            '00AFFF' => ['00AFFF', 39],
            '00D700' => ['00D700', 40],
            '00D75F' => ['00D75F', 41],
            '00D787' => ['00D787', 42],
            '00D7AF' => ['00D7AF', 43],
            '00D7D7' => ['00D7D7', 44],
            '00D7FF' => ['00D7FF', 45],
            '00FF00' => ['00FF00', 46],
            '00FF5F' => ['00FF5F', 47],
            '00FF87' => ['00FF87', 48],
            '00FFAF' => ['00FFAF', 49],
            '00FFD7' => ['00FFD7', 50],
            '00FFFF' => ['00FFFF', 51],
            '5F0000' => ['5F0000', 52],
            '5F005F' => ['5F005F', 53],
            '5F0087' => ['5F0087', 54],
            '5F00AF' => ['5F00AF', 55],
            '5F00D7' => ['5F00D7', 56],
            '5F00FF' => ['5F00FF', 57],
            '5F5F00' => ['5F5F00', 58],
            '5F5F5F' => ['5F5F5F', 59],
            '5F5F87' => ['5F5F87', 60],
            '5F5FAF' => ['5F5FAF', 61],
            '5F5FD7' => ['5F5FD7', 62],
            '5F5FFF' => ['5F5FFF', 63],
            '5F8700' => ['5F8700', 64],
            '5F875F' => ['5F875F', 65],
            '5F8787' => ['5F8787', 66],
            '5F87AF' => ['5F87AF', 67],
            '5F87D7' => ['5F87D7', 68],
            '5F87FF' => ['5F87FF', 69],
            '5FAF00' => ['5FAF00', 70],
            '5FAF5F' => ['5FAF5F', 71],
            '5FAF87' => ['5FAF87', 72],
            '5FAFAF' => ['5FAFAF', 73],
            '5FAFD7' => ['5FAFD7', 74],
            '5FAFFF' => ['5FAFFF', 75],
            '5FD700' => ['5FD700', 76],
            '5FD75F' => ['5FD75F', 77],
            '5FD787' => ['5FD787', 78],
            '5FD7AF' => ['5FD7AF', 79],
            '5FD7D7' => ['5FD7D7', 80],
            '5FD7FF' => ['5FD7FF', 81],
            '5FFF00' => ['5FFF00', 82],
            '5FFF5F' => ['5FFF5F', 83],
            '5FFF87' => ['5FFF87', 84],
            '5FFFAF' => ['5FFFAF', 85],
            '5FFFD7' => ['5FFFD7', 86],
            '5FFFFF' => ['5FFFFF', 87],
            '870000' => ['870000', 88],
            '87005F' => ['87005F', 89],
            '870087' => ['870087', 90],
            '8700AF' => ['8700AF', 91],
            '8700D7' => ['8700D7', 92],
            '8700FF' => ['8700FF', 93],
            '875F00' => ['875F00', 94],
            '875F5F' => ['875F5F', 95],
            '875F87' => ['875F87', 96],
            '875FAF' => ['875FAF', 97],
            '875FD7' => ['875FD7', 98],
            '875FFF' => ['875FFF', 99],
            '878700' => ['878700', 100],
            '87875F' => ['87875F', 101],
            '878787' => ['878787', 102],
            '8787AF' => ['8787AF', 103],
            '8787D7' => ['8787D7', 104],
            '8787FF' => ['8787FF', 105],
            '87AF00' => ['87AF00', 106],
            '87AF5F' => ['87AF5F', 107],
            '87AF87' => ['87AF87', 108],
            '87AFAF' => ['87AFAF', 109],
            '87AFD7' => ['87AFD7', 110],
            '87AFFF' => ['87AFFF', 111],
            '87D700' => ['87D700', 112],
            '87D75F' => ['87D75F', 113],
            '87D787' => ['87D787', 114],
            '87D7AF' => ['87D7AF', 115],
            '87D7D7' => ['87D7D7', 116],
            '87D7FF' => ['87D7FF', 117],
            '87FF00' => ['87FF00', 118],
            '87FF5F' => ['87FF5F', 119],
            '87FF87' => ['87FF87', 120],
            '87FFAF' => ['87FFAF', 121],
            '87FFD7' => ['87FFD7', 122],
            '87FFFF' => ['87FFFF', 123],
            'AF0000' => ['AF0000', 124],
            'AF005F' => ['AF005F', 125],
            'AF0087' => ['AF0087', 126],
            'AF00AF' => ['AF00AF', 127],
            'AF00D7' => ['AF00D7', 128],
            'AF00FF' => ['AF00FF', 129],
            'AF5F00' => ['AF5F00', 130],
            'AF5F5F' => ['AF5F5F', 131],
            'AF5F87' => ['AF5F87', 132],
            'AF5FAF' => ['AF5FAF', 133],
            'AF5FD7' => ['AF5FD7', 134],
            'AF5FFF' => ['AF5FFF', 135],
            'AF8700' => ['AF8700', 136],
            'AF875F' => ['AF875F', 137],
            'AF8787' => ['AF8787', 138],
            'AF87AF' => ['AF87AF', 139],
            'AF87D7' => ['AF87D7', 140],
            'AF87FF' => ['AF87FF', 141],
            'AFAF00' => ['AFAF00', 142],
            'AFAF5F' => ['AFAF5F', 143],
            'AFAF87' => ['AFAF87', 144],
            'AFAFAF' => ['AFAFAF', 145],
            'AFAFD7' => ['AFAFD7', 146],
            'AFAFFF' => ['AFAFFF', 147],
            'AFD700' => ['AFD700', 148],
            'AFD75F' => ['AFD75F', 149],
            'AFD787' => ['AFD787', 150],
            'AFD7AF' => ['AFD7AF', 151],
            'AFD7D7' => ['AFD7D7', 152],
            'AFD7FF' => ['AFD7FF', 153],
            'AFFF00' => ['AFFF00', 154],
            'AFFF5F' => ['AFFF5F', 155],
            'AFFF87' => ['AFFF87', 156],
            'AFFFAF' => ['AFFFAF', 157],
            'AFFFD7' => ['AFFFD7', 158],
            'AFFFFF' => ['AFFFFF', 159],
            'D70000' => ['D70000', 160],
            'D7005F' => ['D7005F', 161],
            'D70087' => ['D70087', 162],
            'D700AF' => ['D700AF', 163],
            'D700D7' => ['D700D7', 164],
            'D700FF' => ['D700FF', 165],
            'D75F00' => ['D75F00', 166],
            'D75F5F' => ['D75F5F', 167],
            'D75F87' => ['D75F87', 168],
            'D75FAF' => ['D75FAF', 169],
            'D75FD7' => ['D75FD7', 170],
            'D75FFF' => ['D75FFF', 171],
            'D78700' => ['D78700', 172],
            'D7875F' => ['D7875F', 173],
            'D78787' => ['D78787', 174],
            'D787AF' => ['D787AF', 175],
            'D787D7' => ['D787D7', 176],
            'D787FF' => ['D787FF', 177],
            'D7AF00' => ['D7AF00', 178],
            'D7AF5F' => ['D7AF5F', 179],
            'D7AF87' => ['D7AF87', 180],
            'D7AFAF' => ['D7AFAF', 181],
            'D7AFD7' => ['D7AFD7', 182],
            'D7AFFF' => ['D7AFFF', 183],
            'D7D700' => ['D7D700', 184],
            'D7D75F' => ['D7D75F', 185],
            'D7D787' => ['D7D787', 186],
            'D7D7AF' => ['D7D7AF', 187],
            'D7D7D7' => ['D7D7D7', 188],
            'D7D7FF' => ['D7D7FF', 189],
            'D7FF00' => ['D7FF00', 190],
            'D7FF5F' => ['D7FF5F', 191],
            'D7FF87' => ['D7FF87', 192],
            'D7FFAF' => ['D7FFAF', 193],
            'D7FFD7' => ['D7FFD7', 194],
            'D7FFFF' => ['D7FFFF', 195],
            'FF0000' => ['FF0000', 196],
            'FF005F' => ['FF005F', 197],
            'FF0087' => ['FF0087', 198],
            'FF00AF' => ['FF00AF', 199],
            'FF00D7' => ['FF00D7', 200],
            'FF00FF' => ['FF00FF', 201],
            'FF5F00' => ['FF5F00', 202],
            'FF5F5F' => ['FF5F5F', 203],
            'FF5F87' => ['FF5F87', 204],
            'FF5FAF' => ['FF5FAF', 205],
            'FF5FD7' => ['FF5FD7', 206],
            'FF5FFF' => ['FF5FFF', 207],
            'FF8700' => ['FF8700', 208],
            'FF875F' => ['FF875F', 209],
            'FF8787' => ['FF8787', 210],
            'FF87AF' => ['FF87AF', 211],
            'FF87D7' => ['FF87D7', 212],
            'FF87FF' => ['FF87FF', 213],
            'FFAF00' => ['FFAF00', 214],
            'FFAF5F' => ['FFAF5F', 215],
            'FFAF87' => ['FFAF87', 216],
            'FFAFAF' => ['FFAFAF', 217],
            'FFAFD7' => ['FFAFD7', 218],
            'FFAFFF' => ['FFAFFF', 219],
            'FFD700' => ['FFD700', 220],
            'FFD75F' => ['FFD75F', 221],
            'FFD787' => ['FFD787', 222],
            'FFD7AF' => ['FFD7AF', 223],
            'FFD7D7' => ['FFD7D7', 224],
            'FFD7FF' => ['FFD7FF', 225],
            'FFFF00' => ['FFFF00', 226],
            'FFFF5F' => ['FFFF5F', 227],
            'FFFF87' => ['FFFF87', 228],
            'FFFFAF' => ['FFFFAF', 229],
            'FFFFD7' => ['FFFFD7', 230],
            'FFFFFF' => ['FFFFFF', 231],
            '080808' => ['080808', 232],
            '121212' => ['121212', 233],
            '1C1C1C' => ['1C1C1C', 234],
            '262626' => ['262626', 235],
            '303030' => ['303030', 236],
            '3A3A3A' => ['3A3A3A', 237],
            '444444' => ['444444', 238],
            '4E4E4E' => ['4E4E4E', 239],
            '585858' => ['585858', 240],
            '626262' => ['626262', 241],
            '6C6C6C' => ['6C6C6C', 242],
            '767676' => ['767676', 243],
            '808080' => ['808080', 244],
            '8A8A8A' => ['8A8A8A', 245],
            '949494' => ['949494', 246],
            '9E9E9E' => ['9E9E9E', 247],
            'A8A8A8' => ['A8A8A8', 248],
            'B2B2B2' => ['B2B2B2', 249],
            'BCBCBC' => ['BCBCBC', 250],
            'C6C6C6' => ['C6C6C6', 251],
            'D0D0D0' => ['D0D0D0', 252],
            'DADADA' => ['DADADA', 253],
            'E4E4E4' => ['E4E4E4', 254],
            'EEEEEE' => ['EEEEEE', 255],
        ];
    }

    /**
     * @dataProvider exactHexCodes
     */
    public function testExactHexCodeInputs($hex, $gcode)
    {
        $color = Xterm256::calculate($hex);
        $r     = new ReflectionClass($color);
        $gcode = sprintf('%%s;5;%s', $gcode);
        $this->assertEquals($gcode, $r->getStaticPropertyValue('color'));
    }
}
