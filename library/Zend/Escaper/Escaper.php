<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Escaper;


/**
 * Context specific methods for use in secure output escaping
 */
class Escaper
{
    /**
     * Entity Map mapping Unicode codepoints to any available named HTML entities.
     *
     * While HTML supports far more named entities, the lowest common denominator
     * has become HTML5's XML Serialisation which is restricted to the those named
     * entities that XML supports. Using HTML entities would result in this error:
     *     XML Parsing Error: undefined entity
     *
     * @var array
     */
    protected static $htmlNamedEntityMap = array(
        34 => 'quot',
        38 => 'amp',
        60 => 'lt',
        62 => 'gt',
        160 => 'nbsp',
        161 => 'iexcl',
        162 => 'cent',
        163 => 'pound',
        164 => 'curren',
        165 => 'yen',
        166 => 'brvbar',
        167 => 'sect',
        168 => 'uml',
        169 => 'copy',
        170 => 'ordf',
        171 => 'laquo',
        172 => 'not',
        173 => 'shy',
        174 => 'reg',
        175 => 'macr',
        176 => 'deg',
        177 => 'plusmn',
        178 => 'sup2',
        179 => 'sup3',
        180 => 'acute',
        181 => 'micro',
        182 => 'para',
        183 => 'middot',
        184 => 'cedil',
        185 => 'sup1',
        186 => 'ordm',
        187 => 'raquo',
        188 => 'frac14',
        189 => 'frac12',
        190 => 'frac34',
        191 => 'iquest',
        192 => 'Agrave',
        193 => 'Aacute',
        194 => 'Acirc',
        195 => 'Atilde',
        196 => 'Auml',
        197 => 'Aring',
        198 => 'AElig',
        199 => 'Ccedil',
        200 => 'Egrave',
        201 => 'Eacute',
        202 => 'Ecirc',
        203 => 'Euml',
        204 => 'Igrave',
        205 => 'Iacute',
        206 => 'Icirc',
        207 => 'Iuml',
        208 => 'ETH',
        209 => 'Ntilde',
        210 => 'Ograve',
        211 => 'Oacute',
        212 => 'Ocirc',
        213 => 'Otilde',
        214 => 'Ouml',
        215 => 'times',
        216 => 'Oslash',
        217 => 'Ugrave',
        218 => 'Uacute',
        219 => 'Ucirc',
        220 => 'Uuml',
        221 => 'Yacute',
        222 => 'THORN',
        223 => 'szlig',
        224 => 'agrave',
        225 => 'aacute',
        226 => 'acirc',
        227 => 'atilde',
        228 => 'auml',
        229 => 'aring',
        230 => 'aelig',
        231 => 'ccedil',
        232 => 'egrave',
        233 => 'eacute',
        234 => 'ecirc',
        235 => 'euml',
        236 => 'igrave',
        237 => 'iacute',
        238 => 'icirc',
        239 => 'iuml',
        240 => 'eth',
        241 => 'ntilde',
        242 => 'ograve',
        243 => 'oacute',
        244 => 'ocirc',
        245 => 'otilde',
        246 => 'ouml',
        247 => 'divide',
        248 => 'oslash',
        249 => 'ugrave',
        250 => 'uacute',
        251 => 'ucirc',
        252 => 'uuml',
        253 => 'yacute',
        254 => 'thorn',
        255 => 'yuml',
        338 => 'OElig',
        339 => 'oelig',
        352 => 'Scaron',
        353 => 'scaron',
        376 => 'Yuml',
        402 => 'fnof',
        710 => 'circ',
        732 => 'tilde',
        913 => 'Alpha',
        914 => 'Beta',
        915 => 'Gamma',
        916 => 'Delta',
        917 => 'Epsilon',
        918 => 'Zeta',
        919 => 'Eta',
        920 => 'Theta',
        921 => 'Iota',
        922 => 'Kappa',
        923 => 'Lambda',
        924 => 'Mu',
        925 => 'Nu',
        926 => 'Xi',
        927 => 'Omicron',
        928 => 'Pi',
        929 => 'Rho',
        931 => 'Sigma',
        932 => 'Tau',
        933 => 'Upsilon',
        934 => 'Phi',
        935 => 'Chi',
        936 => 'Psi',
        937 => 'Omega',
        945 => 'alpha',
        946 => 'beta',
        947 => 'gamma',
        948 => 'delta',
        949 => 'epsilon',
        950 => 'zeta',
        951 => 'eta',
        952 => 'theta',
        953 => 'iota',
        954 => 'kappa',
        955 => 'lambda',
        956 => 'mu',
        957 => 'nu',
        958 => 'xi',
        959 => 'omicron',
        960 => 'pi',
        961 => 'rho',
        962 => 'sigmaf',
        963 => 'sigma',
        964 => 'tau',
        965 => 'upsilon',
        966 => 'phi',
        967 => 'chi',
        968 => 'psi',
        969 => 'omega',
        977 => 'thetasym',
        978 => 'upsih',
        982 => 'piv',
        8194 => 'ensp',
        8195 => 'emsp',
        8201 => 'thinsp',
        8204 => 'zwnj',
        8205 => 'zwj',
        8206 => 'lrm',
        8207 => 'rlm',
        8211 => 'ndash',
        8212 => 'mdash',
        8216 => 'lsquo',
        8217 => 'rsquo',
        8218 => 'sbquo',
        8220 => 'ldquo',
        8221 => 'rdquo',
        8222 => 'bdquo',
        8224 => 'dagger',
        8225 => 'Dagger',
        8226 => 'bull',
        8230 => 'hellip',
        8240 => 'permil',
        8242 => 'prime',
        8243 => 'Prime',
        8249 => 'lsaquo',
        8250 => 'rsaquo',
        8254 => 'oline',
        8260 => 'frasl',
        8364 => 'euro',
        8465 => 'image',
        8472 => 'weierp',
        8476 => 'real',
        8482 => 'trade',
        8501 => 'alefsym',
        8592 => 'larr',
        8593 => 'uarr',
        8594 => 'rarr',
        8595 => 'darr',
        8596 => 'harr',
        8629 => 'crarr',
        8656 => 'lArr',
        8657 => 'uArr',
        8658 => 'rArr',
        8659 => 'dArr',
        8660 => 'hArr',
        8704 => 'forall',
        8706 => 'part',
        8707 => 'exist',
        8709 => 'empty',
        8711 => 'nabla',
        8712 => 'isin',
        8713 => 'notin',
        8715 => 'ni',
        8719 => 'prod',
        8721 => 'sum',
        8722 => 'minus',
        8727 => 'lowast',
        8730 => 'radic',
        8733 => 'prop',
        8734 => 'infin',
        8736 => 'ang',
        8743 => 'and',
        8744 => 'or',
        8745 => 'cap',
        8746 => 'cup',
        8747 => 'int',
        8756 => 'there4',
        8764 => 'sim',
        8773 => 'cong',
        8776 => 'asymp',
        8800 => 'ne',
        8801 => 'equiv',
        8804 => 'le',
        8805 => 'ge',
        8834 => 'sub',
        8835 => 'sup',
        8836 => 'nsub',
        8838 => 'sube',
        8839 => 'supe',
        8853 => 'oplus',
        8855 => 'otimes',
        8869 => 'perp',
        8901 => 'sdot',
        8968 => 'lceil',
        8969 => 'rceil',
        8970 => 'lfloor',
        8971 => 'rfloor',
        9001 => 'lang',
        9002 => 'rang',
        9674 => 'loz',
        9824 => 'spades',
        9827 => 'clubs',
        9829 => 'hearts',
        9830 => 'diams',        
    );

    /**
     * Current encoding for escaping. If not UTF-8, we convert strings from this encoding
     * pre-escaping and back to this encoding post-escaping.
     *
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * Holds the value of the special flags passed as second parameter to
     * htmlspecialchars(). We modify these for PHP 5.4 to take advantage
     * of the new ENT_SUBSTITUTE flag for correctly dealing with invalid
     * UTF-8 sequences.
     *
     * @var string
     */
    protected $htmlSpecialCharsFlags = ENT_QUOTES;

    /**
     * Static Matcher which escapes characters for HTML Attribute contexts
     *
     * @var callable
     */
    protected $htmlAttrMatcher;

    /**
     * Static Matcher which escapes characters for Javascript contexts
     *
     * @var callable
     */
    protected $jsMatcher;

    /**
     * Static Matcher which escapes characters for CSS Attribute contexts
     *
     * @var callable
     */
    protected $cssMatcher;

    /**
     * List of all encoding supported by this class
     *
     * @var array
     */
    protected $supportedEncodings = array(
        'iso-8859-1',   'iso8859-1',    'iso-8859-5',   'iso8859-5',
        'iso-8859-15',  'iso8859-15',   'utf-8',        'cp866',
        'ibm866',       '866',          'cp1251',       'windows-1251',
        'win-1251',     '1251',         'cp1252',       'windows-1252',
        '1252',         'koi8-r',       'koi8-ru',      'koi8r',
        'big5',         '950',          'gb2312',       '936',
        'big5-hkscs',   'shift_jis',    'sjis',         'sjis-win',
        'cp932',        '932',          'euc-jp',       'eucjp',
        'eucjp-win',    'macroman'
    );

    /**
     * Constructor: Single parameter allows setting of global encoding for use by
     * the current object. If PHP 5.4 is detected, additional ENT_SUBSTITUTE flag
     * is set for htmlspecialchars() calls.
     *
     * @param string $encoding
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($encoding = null)
    {
        if ($encoding !== null) {
            $encoding = (string) $encoding;
            if ($encoding === '') {
                throw new Exception\InvalidArgumentException(
                    get_class($this) . ' constructor parameter does not allow a blank value'
                );
            }

            $encoding = strtolower($encoding);
            if (!in_array($encoding, $this->supportedEncodings)) {
                throw new Exception\InvalidArgumentException(
                    'Value of \'' . $encoding . '\' passed to ' . get_class($this)
                    . ' constructor parameter is invalid. Provide an encoding supported by htmlspecialchars()'
                );
            }

            $this->encoding = $encoding;
        }

        if (defined('ENT_SUBSTITUTE')) {
            $this->htmlSpecialCharsFlags|= ENT_SUBSTITUTE;
        }

        // set matcher callbacks
        $this->htmlAttrMatcher = array($this, 'htmlAttrMatcher');
        $this->jsMatcher       = array($this, 'jsMatcher');
        $this->cssMatcher      = array($this, 'cssMatcher');
    }

    /**
     * Return the encoding that all output/input is expected to be encoded in.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Escape a string for the HTML Body context where there are very few characters
     * of special meaning. Internally this will use htmlspecialchars().
     *
     * @param string $string
     * @return string
     */
    public function escapeHtml($string)
    {
        return htmlspecialchars($string, $this->htmlSpecialCharsFlags, $this->encoding);
    }

    /**
     * Escape a string for the HTML Attribute context. We use an extended set of characters
     * to escape that are not covered by htmlspecialchars() to cover cases where an attribute
     * might be unquoted or quoted illegally (e.g. backticks are valid quotes for IE).
     *
     * @param string $string
     * @return string
     */
    public function escapeHtmlAttr($string)
    {
        $string = $this->toUtf8($string);
        if ($string === '' || ctype_digit($string)) {
            return $string;
        }

        $result = preg_replace_callback('/[^a-z0-9,\.\-_]/iSu', $this->htmlAttrMatcher, $string);
        return $this->fromUtf8($result);
    }

    /**
     * Escape a string for the Javascript context. This does not use json_encode(). An extended
     * set of characters are escaped beyond ECMAScript's rules for Javascript literal string
     * escaping in order to prevent misinterpretation of Javascript as HTML leading to the
     * injection of special characters and entities. The escaping used should be tolerant
     * of cases where HTML escaping was not applied on top of Javascript escaping correctly.
     * Backslash escaping is not used as it still leaves the escaped character as-is and so
     * is not useful in a HTML context.
     *
     * @param string $string
     * @return string
     */
    public function escapeJs($string)
    {
        $string = $this->toUtf8($string);
        if ($string === '' || ctype_digit($string)) {
            return $string;
        }

        $result = preg_replace_callback('/[^a-z0-9,\._]/iSu', $this->jsMatcher, $string);
        return $this->fromUtf8($result);
    }

    /**
     * Escape a string for the URI or Parameter contexts. This should not be used to escape
     * an entire URI - only a subcomponent being inserted. The function is a simple proxy
     * to rawurlencode() which now implements RFC 3986 since PHP 5.3 completely.
     *
     * @param string $string
     * @return string
     */
    public function escapeUrl($string)
    {
        return rawurlencode($string);
    }

    /**
     * Escape a string for the CSS context. CSS escaping can be applied to any string being
     * inserted into CSS and escapes everything except alphanumerics.
     *
     * @param string $string
     * @return string
     */
    public function escapeCss($string)
    {
        $string = $this->toUtf8($string);
        if ($string === '' || ctype_digit($string)) {
            return $string;
        }

        $result = preg_replace_callback('/[^a-z0-9]/iSu', $this->cssMatcher, $string);
        return $this->fromUtf8($result);
    }

    /**
     * Callback function for preg_replace_callback that applies HTML Attribute
     * escaping to all matches.
     *
     * @param array $matches
     * @return string
     */
    protected function htmlAttrMatcher($matches)
    {
        $chr = $matches[0];
        $ord = ord($chr);

        /**
         * The following replaces characters undefined in HTML with the
         * hex entity for the Unicode replacement character.
         */
        if (($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r")
            || ($ord >= 0x7f && $ord <= 0x9f)
        ) {
            return '&#xFFFD;';
        }

        /**
         * Check if the current character to escape has a name entity we should
         * replace it with while grabbing the integer value of the character.
         */
        if (strlen($chr) > 1) {
            $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
        }

        $hex = bin2hex($chr);
        $ord = hexdec($hex);
        if (isset(static::$htmlNamedEntityMap[$ord])) {
            return '&' . static::$htmlNamedEntityMap[$ord] . ';';
        }

        /**
         * Per OWASP recommendations, we'll use upper hex entities
         * for any other characters where a named entity does not exist.
         */
        if ($ord > 255) {
            return sprintf('&#x%04X;', $ord);
        }
        return sprintf('&#x%02X;', $ord);
    }

    /**
     * Callback function for preg_replace_callback that applies Javascript
     * escaping to all matches.
     *
     * @param array $matches
     * @return string
     */
    protected function jsMatcher($matches)
    {
        $chr = $matches[0];
        if (strlen($chr) == 1) {
            return sprintf('\\x%02X', ord($chr));
        }
        $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
        return sprintf('\\u%04s', strtoupper(bin2hex($chr)));
    }

    /**
     * Callback function for preg_replace_callback that applies CSS
     * escaping to all matches.
     *
     * @param array $matches
     * @return string
     */
    protected function cssMatcher($matches)
    {
        $chr = $matches[0];
        if (strlen($chr) == 1) {
            $ord = ord($chr);
        } else {
            $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
            $ord = hexdec(bin2hex($chr));
        }
        return sprintf('\\%X ', $ord);
    }

    /**
     * Converts a string to UTF-8 from the base encoding. The base encoding is set via this
     * class' constructor.
     *
     * @param string $string
     * @throws Exception\RuntimeException
     * @return string
     */
    protected function toUtf8($string)
    {
        if ($this->getEncoding() === 'utf-8') {
            $result = $string;
        } else {
            $result = $this->convertEncoding($string, 'UTF-8', $this->getEncoding());
        }

        if (!$this->isUtf8($result)) {
            throw new Exception\RuntimeException(sprintf(
                'String to be escaped was not valid UTF-8 or could not be converted: %s', $result
            ));
        }

        return $result;
    }

    /**
     * Converts a string from UTF-8 to the base encoding. The base encoding is set via this
     * class' constructor.
     * @param string $string
     * @return string
     */
    protected function fromUtf8($string)
    {
        if ($this->getEncoding() === 'utf-8') {
            return $string;
        }

        return $this->convertEncoding($string, $this->getEncoding(), 'UTF-8');
    }

    /**
     * Checks if a given string appears to be valid UTF-8 or not.
     *
     * @param string $string
     * @return bool
     */
    protected function isUtf8($string)
    {
        return ($string === '' || preg_match('/^./su', $string));
    }

    /**
     * Encoding conversion helper which wraps iconv and mbstring where they exist or throws
     * and exception where neither is available.
     *
     * @param string $string
     * @param string $to
     * @param array|string $from
     * @throws Exception\RuntimeException
     * @return string
     */
    protected function convertEncoding($string, $to, $from)
    {
        if (function_exists('iconv')) {
            $result = iconv($from, $to, $string);
        } elseif (function_exists('mb_convert_encoding')) {
            $result = mb_convert_encoding($string, $to, $from);
        } else {
            throw new Exception\RuntimeException(
                get_class($this)
                . ' requires either the iconv or mbstring extension to be installed'
                . ' when escaping for non UTF-8 strings.'
            );
        }

        if ($result === false) {
            return ''; // return non-fatal blank string on encoding errors from users
        }
        return $result;
    }
}
