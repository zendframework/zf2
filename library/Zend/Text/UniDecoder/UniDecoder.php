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
 * UTF-8 Unicode to ASCII decoder that does transliteration of readable characters to their ASCII equivalents.
 *
 * This class should be considered static - that's why it's declared abstract to prevent from instantiating.
 * Ported from the Python UniDecode implementation.
 */
abstract class UniDecoder
{
    /**
     * Transliteration tables.
     * 
     * @var array
     */
    protected static $tables = array();

    /**
     * Decode an UTF-8 encoded unicode string to plain ASCII, transliterating special accents and special characters
     * to their ASCII equivalents. Note: the resulting string lenght might be greater than the original one,
     * because of how the transliteration works for some international characters.
     *
     * @param   string  $string               UTF-8 string to decode.
     * @param   string  $unknownPlaceholder   Character to use in case a character cannot be decoded.
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public static function decode($string, $unknownPlaceholder = '')
    {
        $return = '';

        // Check for an empty value
        if ($string === '' || $string === null || $string === false) {
            return '';
        }

        // Check if it is a scalar
        if (!is_scalar($string)) {
            throw new Exception\InvalidArgumentException('Cannot decode ' . gettype($string));
        }

        // Extract all UTF-8 characters
        if (preg_match_all('#\P{Co}#u', (string)$string, $split) || !isset($split[0])) {
            // Double check if we do not have an empty unicode string
            $split = isset($split[0]) ? $split[0] : array();
        } else {
            // Something went wrong. Probably string contains incomplete or invalid multibyte sequences that throw off
            // PREG UTF-8 engine. We will attempt to fix the UTF-8 string using iconv() with //IGNORE mode.
            $fixedString = @iconv("UTF-8", "UTF-8//IGNORE", $string);

            // If the operation was successful, attempt to perform matching again.
            if ($fixedString !== false && (preg_match_all('#\P{Co}#u', $fixedString, $split) || !isset($split[0]))) {
                // Double check if we do not have an empty unicode string
                $split = isset($split[0]) ? $split[0] : array();
            } else {
                // Could not extract characters from the string - this means it's probably a malformed UTF-8. We will
                // attempt to split it as ASCII and process ASCII values while truncating or replacing unknown chars
                // with placeholders.
                $split = str_split((string)$string);
            }
        }

        foreach ($split as $char) {
            $codepoint = static::uniOrd($char);

            if ($codepoint === false) {
                // Malformed character
                $return .= $unknownPlaceholder;
                continue;
            }

            if ($codepoint < 0x80) {
                // Basic ASCII
                $return .= chr($codepoint);
                continue;
            }

            if ($codepoint > 0xeffff) {
                // Characters in Private Use Area and above are ignored
                $return .= $unknownPlaceholder;
                continue;
            }

            $section  = $codepoint >> 8;  // Chop off the last two hex digits
            $position = $codepoint % 256; // Last two hex digits

            if (!isset(self::$tables[$section])) {
                self::$tables[$section] = @include sprintf('%s/tables/x%03x.php', __DIR__, $section);
            }

            if (isset(self::$tables[$section][$position])) {
                $return .= self::$tables[$section][$position];
            }else{
                $return .= $unknownPlaceholder;
            }
        }

        return $return;
    }
    
    /**
     * Determine unicode codepoint from an UTF-8 multibyte character.
     *
     * @param  string               $char  UTF-8 multibyte character
     * @return integer|boolean
     */
    protected static function uniOrd($char)
    {
        $h = ord($char[0]);
        
        if ($h <= 0x7f) {
            return $h;
        } elseif ($h < 0xc2) {
            return false;
        } elseif ($h <= 0xdf) {
            if (!isset($char[1])) {
                return false; // malformed
            }
            return ($h & 0x1f) << 6 | (ord($char[1]) & 0x3f);
        } elseif ($h <= 0xef) {
            if (!isset($char[1]) || !isset($char[2])) {
                return false; // malformed
            }
            return ($h & 0x0f) << 12 | (ord($char[1]) & 0x3f) << 6
                                     | (ord($char[2]) & 0x3f);
        } elseif ($h <= 0xf4) {
            if (!isset($char[1]) || !isset($char[2]) || !isset($char[3])) {
                return false; // malformed
            }
            return ($h & 0x0f) << 18 | (ord($char[1]) & 0x3f) << 12
                                     | (ord($char[2]) & 0x3f) << 6
                                     | (ord($char[3]) & 0x3f);
        } else {
            return false; // unknown
        }
    }
}
