<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text
 */

namespace Zend\Text\UniDecoder;

/**
 * Unicode to ASCII decoder.
 * 
 * Ported from the Python UniDecode implementation.
 */
class UniDecoder
{
    /**
     * Transliteration tables.
     * 
     * @var array
     */
    protected static $tables = array();
    
    /**
     * Decode an UTF-8 encoded unicode string to ASCII.
     * 
     * @param  string $string
     * @return string
     */
    public function decode($string)
    {
        $return = '';

        foreach (preg_split('()u', $string, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            $codepoint = $this->uniOrd($char);
            
            if ($codepoint < 0x80) {
                // Basic ASCII
                $return .= chr($codepoint);
                continue;
            }
            
            if ($codepoint > 0xeffff) {
                // Characters in Private Use Area and above are ignored
                continue;
            }

            $section  = $codepoint >> 8;  // Chop off the last two hex digits
            $position = $codepoint % 256; // Last two hex digits

            if (!isset(self::$tables[$section])) {
                self::$tables[$section] = @include sprintf('%s/tables/x%03x.php', __DIR__, $section);
            }

            if (isset(self::$tables[$section][$position])) {
                $return .= self::$tables[$section][$position];
            }
        }

        return $return;
    }
    
    /**
     * Get unicode codepoint from character.
     * 
     * @param  string $char
     * @return integer
     */
    protected function uniOrd($char)
    {
        $h = ord($char[0]);
        
        if ($h <= 0x7f) {
            return $h;
        } else if ($h < 0xc2) {
            return null;
        } else if ($h <= 0xdf) {
            return ($h & 0x1f) << 6 | (ord($char[1]) & 0x3f);
        } else if ($h <= 0xef) {
            return ($h & 0x0f) << 12 | (ord($char[1]) & 0x3f) << 6
                                     | (ord($char[2]) & 0x3f);
        } else if ($h <= 0xf4) {
            return ($h & 0x0f) << 18 | (ord($char[1]) & 0x3f) << 12
                                     | (ord($char[2]) & 0x3f) << 6
                                     | (ord($char[3]) & 0x3f);
        } else {
            return null;
        }
    }
}
