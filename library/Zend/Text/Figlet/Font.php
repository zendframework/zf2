<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text_Figlet
 */

namespace Zend\Text\Figlet;

/**
 * FIGlet font routines
 * loads default ZF font if not font provided
 *
 * @category  Zend
 * @package   Zend_Text_Figlet
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Font
{
    /**
     * FIGlet font file signature
     */
    const SIGNATURE = 'flf2';


    /**
     * Signature of the font file
     * @var string
     */
    protected $signature;

    /**
     * Hard blank character
     *
     * @var string
     */
    protected $hardBlank;

    /**
     * Font parameters parsed from font header.
     *
     * @var array
     */
    protected $params = array(
        'height' => null,
        'baseline' => null,
        'max_length' => null,
        'old_layout' => null,
        'comment_lines' => null,
        'print_direction' => null,
        'full_layout' => null,
        'codetag_count' => null,
    );

    /**
     * Font comments
     *
     * @var string
     */
    protected $comments;

    /**
     * Font character data. Array containing all characters of the current font.
     *
     * @var array
     */
    protected $characterData = array();

    /**
     * Latin-1 codes for German letters, respectively:
     *
     * LATIN CAPITAL LETTER A WITH DIAERESIS = A-umlaut
     * LATIN CAPITAL LETTER O WITH DIAERESIS = O-umlaut
     * LATIN CAPITAL LETTER U WITH DIAERESIS = U-umlaut
     * LATIN SMALL LETTER A WITH DIAERESIS = a-umlaut
     * LATIN SMALL LETTER O WITH DIAERESIS = o-umlaut
     * LATIN SMALL LETTER U WITH DIAERESIS = u-umlaut
     * LATIN SMALL LETTER SHARP S = ess-zed
     *
     * @var array
     */
    protected $germanChars = array(196, 214, 220, 228, 246, 252, 223);


    /**
     * Constructor
     *
     * @param string $fontFile
     */
    public function __construct($fontFile = null)
    {
        if (null !== $fontFile) {
            $this->fromFile($fontFile);
        } else {
            $this->loadDefaultFont();
        }
    }

    /**
     * Get font parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get single font parameter
     *
     * @param string $name
     * @return int
     * @throws Exception\InvalidArgumentException
     */
    public function getParam($name)
    {
        if (!isset($this->params[$name])) {
            throw new Exception\InvalidArgumentException("Unknown parameter '{$name}'");
        }

        return $this->params[$name];
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Get hard blank character
     *
     * @return string
     */
    public function getHardBlank()
    {
        return $this->hardBlank;
    }

    /**
     * Load font from file
     *
     * @param string $fontFile
     * @return Font
     * @throws Exception\RuntimeException
     */
    public function fromFile($fontFile)
    {
        // Check if the font file exists
        if (!is_readable($fontFile)) {
            throw new Exception\RuntimeException("Font file '{$fontFile}' not found or not readable");
        }

        // Check if file is compressed and gzip support is required
        if (substr($fontFile, -3) === '.gz') {
            if (!function_exists('gzcompress')) {
                throw new Exception\RuntimeException(
                    'GZIP library is required for gzip compressed font files'
                );
            }

            $fontFile   = 'compress.zlib://' . $fontFile;
        }

        $contents = file_get_contents($fontFile);
        if (false === $contents) {
            throw new Exception\RuntimeException('Could not open file:' . $fontFile);
        }

        return $this->fromString($contents);
    }

    /**
     * Load font from string.
     * Parse header, comments and character data.
     *
     * @param string $string
     * @return Font
     * @throws Exception\UnexpectedValueException
     */
    public function fromString($string)
    {
        $lines  = explode("\n", trim($string));
        $header = explode(' ', array_shift($lines));

        if (strpos($header[0], self::SIGNATURE) !== 0 || count($header) < 6) {
            throw new Exception\UnexpectedValueException('Not a FIGlet 2 font');
        }

        $this->signature               = substr($header[0], 0, 5);
        $this->hardBlank               = substr($header[0], -1);
        $this->params['height']        = max(1, (int) $header[1]);
        $this->params['baseline']      = (int) $header[2];
        $this->params['max_length']    = max(1, (int) $header[3]);
        $this->params['old_layout']    = (int) $header[4];
        $this->params['comment_lines'] = (int) $header[5];

        if (isset($header[6])) {
            $this->params['print_direction'] = (int) $header[6];
        } else {
            $this->params['print_direction'] = 0;
        }

        // If no smush2 mode, decode smush into smush2
        if (isset($header[7])) {
            $this->params['full_layout'] = (int) $header[7];
        } else {
            if ($this->params['old_layout'] === 2) {
                $this->params['full_layout'] = FigletOptions::SM_KERN;
            } else if ($this->params['old_layout'] < 0) {
                $this->params['full_layout'] = 0;
            } else {
                $this->params['full_layout'] = (($this->params['old_layout'] & 31) | FigletOptions::SM_SMUSH);
            }
        }

        // parse comments
        $commentLines = array();
        for ($i = 0; $i < $this->params['comment_lines']; $i++) {
            $commentLines[] = rtrim(array_shift($lines));

        }
        $this->comments = implode("\n", $commentLines);

        // parse character data
        $this->loadCharacterData($lines);

        return $this;
    }

    /**
     * Get character representation for given code
     *
     * @param integer $code
     * @return array|null
     */
    public function getChar($code)
    {
        if (isset($this->characterData[$code])) {
            return $this->characterData[$code];
        } else {
            return null;
        }
    }

    /**
     * Parse line array for characters
     *
     * @param array $lines
     * @return bool|null
     */
    protected function loadCharacterData(&$lines)
    {
        // Fetch all ASCII characters
        for ($asciiCode = 32; $asciiCode < 127; $asciiCode++) {
            $this->characterData[$asciiCode] = $this->loadChar($lines);
        }

        // Fetch all german characters
        foreach ($this->germanChars as $uniCode) {
            $char = $this->loadChar($lines);

            if ($char === false) {
                return null;
            }

            if (trim(implode('', $char)) !== '') {
                $this->characterData[$uniCode] = $char;
            }
        }

        // At the end fetch all extended characters
        while (!empty($lines)) {
            $line = array_shift($lines);

            // Get the Unicode
            list($uniCode, ) = explode(' ', $line, 2);

            if (empty($uniCode)) {
                continue;
            }

            // Convert it if required
            if (substr($uniCode, 0, 2) === '0x') {
                $uniCode = hexdec(substr($uniCode, 2));
            } elseif (substr($uniCode, 0, 1) === '0' && $uniCode !== '0' || substr($uniCode, 0, 2) === '-0') {
                $uniCode = octdec($uniCode);
            } else {
                $uniCode = (int) $uniCode;
            }

            // Now fetch the character
            $char = $this->loadChar($lines);

            if ($char === false) {
                return null;
            }

            $this->characterData[$uniCode] = $char;
        }

        return true;
    }

    /**
     * Load a single character from the line array
     *
     * @param  array $lines
     * @return array
     */
    protected function loadChar(&$lines)
    {
        $char = array();
        for ($i = 0; $i < $this->params['height']; $i++) {
            if (empty($lines)) {
                return false;
            }

            $line = rtrim(array_shift($lines), "\r\n");

            if (preg_match('#(.)\\1?$#', $line, $match) === 1) {
                $line = str_replace($match[1], '', $line);
            }

            $char[] = $line;
        }

        return $char;
    }

    /**
     * Load default ZF2 FIGlet font
     */
    protected function loadDefaultFont()
    {
        $this->hardBlank = '$';
        $this->comments  = 'Default ZF2 FIglet font';

        $this->params = array(
            'height'          => 7,
            'baseline'        => 6,
            'max_length'      => 10,
            'old_layout'      => 51,
            'comment_lines'   => 1,
            'print_direction' => 0,
            'full_layout'     => 7987,
            'codetag_count'   => null,
        );

        $this->characterData = array(
            32 =>  array(
                0 => '$ ',
                1 => '$ ',
                2 => '$ ',
                3 => '$ ',
                4 => '$ ',
                5 => '$ ',
                6 => '$ ',
            ),
            33 =>  array(
                0 => '   __   ',
                1 => '  /  \\\\ ',
                2 => '  |  || ',
                3 => '  |$ || ',
                4 => '   \\//  ',
                5 => '   []|  ',
                6 => '        ',
            ),
            34 =>  array(
                0 => '   __  __ ',
                1 => '  / /// //',
                2 => ' /_///_// ',
                3 => ' `-` `-`  ',
                4 => '          ',
                5 => '          ',
                6 => '          ',
            ),
            35 =>  array(
                0 => '',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            36 =>  array(
                0 => '    _     ',
                1 => '   | ||_  ',
                2 => '  / ___// ',
                3 => '  \\___ \\\\ ',
                4 => '  /  $ // ',
                5 => ' /_   //  ',
                6 => ' `-|_||   ',
            ),
            37 =>  array(
                0 => '%',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            38 =>  array(
                0 => '&',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            39 =>  array(
                0 => '\'',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            40 =>  array(
                0 => '(',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            41 =>  array(
                0 => ')',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            42 =>  array(
                0 => '*',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            43 =>  array(
                0 => '          ',
                1 => '    _     ',
                2 => '  _| ||   ',
                3 => ' |_ $ _|| ',
                4 => ' `-|_|-`  ',
                5 => '    -     ',
                6 => '          ',
            ),
            44 =>  array(
                0 => ',',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            45 =>  array(
                0 => '          ',
                1 => '          ',
                2 => ' ,------,,',
                3 => '\'======\'\' ',
                4 => '          ',
                5 => '          ',
                6 => '          ',
            ),
            46 =>  array(
                0 => '.',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            47 =>  array(
                0 => '/',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            48 =>  array(
                0 => '   ___    ',
                1 => '  / _ \\\\  ',
                2 => ' | |$| || ',
                3 => ' | |_| || ',
                4 => '  \\___//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            49 =>  array(
                0 => '   __     ',
                1 => '  /  ||   ',
                2 => '   | ||   ',
                3 => '  _| ||_  ',
                4 => ' |__$__|| ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            50 =>  array(
                0 => ' _____    ',
                1 => ' \\___ \\\\  ',
                2 => ' / ___//  ',
                3 => ' | $  \\\\  ',
                4 => ' |_____\\\\ ',
                5 => ' `------` ',
                6 => '          ',
            ),
            51 =>  array(
                0 => '  _____   ',
                1 => ' |___  \\\\ ',
                2 => '  __$\\ // ',
                3 => '  __$/ \\\\ ',
                4 => ' |_____// ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            52 =>  array(
                0 => '    __    ',
                1 => '   /  ||  ',
                2 => '  / /|||_ ',
                3 => ' /__ $ _||',
                4 => '    |_||  ',
                5 => '    `-`   ',
                6 => '          ',
            ),
            53 =>  array(
                0 => '   _____  ',
                1 => '  / ___// ',
                2 => ' /___ \\\\  ',
                3 => '  / $ //  ',
                4 => ' /___//   ',
                5 => '`----`    ',
                6 => '          ',
            ),
            54 =>  array(
                0 => '    __   ',
                1 => '   / //  ',
                2 => '  / //   ',
                3 => ' / __ \\\\ ',
                4 => ' \\____// ',
                5 => '  `---`  ',
                6 => '         ',
            ),
            55 =>  array(
                0 => '  ______  ',
                1 => ' |___  // ',
                2 => '    / //  ',
                3 => '   | ||   ',
                4 => '   |_||   ',
                5 => '   `-`    ',
                6 => '          ',
            ),
            56 =>  array(
                0 => '  ____   ',
                1 => ' /    \\\\ ',
                2 => ' \\ -- // ',
                3 => ' / -- \\\\ ',
                4 => ' \\____// ',
                5 => ' `----`  ',
                6 => '         ',
            ),
            57 =>  array(
                0 => '  ____    ',
                1 => ' / __ \\\\  ',
                2 => ' \\__   || ',
                3 => '    / //  ',
                4 => '   /_//   ',
                5 => '   `-`    ',
                6 => '          ',
            ),
            58 =>  array(
                0 => '       ',
                1 => '       ',
                2 => '   []| ',
                3 => '       ',
                4 => '   []| ',
                5 => '       ',
                6 => '       ',
            ),
            59 =>  array(
                0 => '       ',
                1 => '   _   ',
                2 => '  [_]| ',
                3 => '   _   ',
                4 => '  | ]] ',
                5 => '  |//  ',
                6 => '  \'    ',
            ),
            60 =>  array(
                0 => '<',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            61 =>  array(
                0 => '          ',
                1 => '  ______  ',
                2 => ' /_____// ',
                3 => ' /_____// ',
                4 => ' `-----`  ',
                5 => '          ',
                6 => '          ',
            ),
            62 =>  array(
                0 => '>',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            63 =>  array(
                0 => '?',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            64 =>  array(
                0 => '@',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            65 =>  array(
                0 => '   ___    ',
                1 => '  / _ \\\\  ',
                2 => ' / //\\ \\\\ ',
                3 => '|  ___  ||',
                4 => '|_||  |_||',
                5 => '`-`   `-` ',
                6 => '          ',
            ),
            66 =>  array(
                0 => ' ______   ',
                1 => '|      \\\\ ',
                2 => '|  --$  // ',
                3 => '|  --  \\\\ ',
                4 => '|______// ',
                5 => '`------`  ',
                6 => '          ',
            ),
            67 =>  array(
                0 => '  _____   ',
                1 => ' / ____|| ',
                2 => '/ //---`\' ',
                3 => '\\ \\\\___   ',
                4 => ' \\_____|| ',
                5 => '  `----`  ',
                6 => '          ',
            ),
            68 =>  array(
                0 => ' _____    ',
                1 => '|  __ \\\\  ',
                2 => '| |$ \\ || ',
                3 => '| |__/ || ',
                4 => '|_____//  ',
                5 => ' -----`   ',
                6 => '          ',
            ),
            69 =>  array(
                0 => '  _____   ',
                1 => ' |  ___|| ',
                2 => ' | ||__   ',
                3 => ' | ||__   ',
                4 => ' |_____|| ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            70 =>  array(
                0 => '  ______  ',
                1 => ' /_____// ',
                2 => ' `____ `  ',
                3 => ' /___//   ',
                4 => ' `__ `    ',
                5 => ' /_//     ',
                6 => ' `-`      ',
            ),
            71 =>  array(
                0 => '  _____   ',
                1 => ' /  ___|| ',
                2 => '| //$__   ',
                3 => '| \\\\_\\ || ',
                4 => ' \\____//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            72 =>  array(
                0 => ' __   _   ',
                1 => '| || | || ',
                2 => '| \'--\' || ',
                3 => '| .--. || ',
                4 => '|_|| |_|| ',
                5 => '`-`  `-`  ',
                6 => '          ',
            ),
            73 =>  array(
                0 => '  ______  ',
                1 => ' /_   _// ',
                2 => '  -| ||-  ',
                3 => '  _| ||_  ',
                4 => ' /_____// ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            74 =>  array(
                0 => '  ______  ',
                1 => ' /_   _// ',
                2 => '   | ||   ',
                3 => '  _| ||   ',
                4 => ' /__//    ',
                5 => ' `--`     ',
                6 => '          ',
            ),
            75 =>  array(
                0 => '  _  __  ',
                1 => ' | |/ // ',
                2 => ' | \' //  ',
                3 => ' | . \\\\  ',
                4 => ' |_|\\_\\\\ ',
                5 => ' `-` --` ',
                6 => '         ',
            ),
            76 =>  array(
                0 => '  __     ',
                1 => ' | ||    ',
                2 => ' | ||    ',
                3 => ' | ||__  ',
                4 => ' |____// ',
                5 => ' `----`  ',
                6 => '         ',
            ),
            77 =>  array(
                0 => ' _    _   ',
                1 => '| \\  / || ',
                2 => '|  \\/  || ',
                3 => '| .  . || ',
                4 => '|_|\\/|_|| ',
                5 => '`-`  `-`  ',
                6 => '          ',
            ),
            78 =>  array(
                0 => '  _  _   ',
                1 => ' | \\| || ',
                2 => ' |  \' || ',
                3 => ' | .  || ',
                4 => ' |_|\\_|| ',
                5 => ' `-` -`  ',
                6 => '         ',
            ),
            79 =>  array(
                0 => '   ___    ',
                1 => '  / _ \\\\  ',
                2 => ' | /$\\ || ',
                3 => ' | \\_/ || ',
                4 => '  \\___//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            80 =>  array(
                0 => '  ____    ',
                1 => ' |  _ \\\\  ',
                2 => ' | |_| || ',
                3 => ' | .__//  ',
                4 => ' |_|--`   ',
                5 => ' `-`      ',
                6 => '          ',
            ),
            81 =>  array(
                0 => '  ___    ',
                1 => ' / _ \\\\  ',
                2 => '| /$\\ || ',
                3 => '| \\_/ || ',
                4 => ' \\___ \\\\ ',
                5 => ' `---`   ',
                6 => '         ',
            ),
            82 =>  array(
                0 => '  ____    ',
                1 => ' |  _ \\\\  ',
                2 => ' | |_| || ',
                3 => ' | .  //  ',
                4 => ' |_|\\_\\\\  ',
                5 => ' `-` --`  ',
                6 => '          ',
            ),
            83 =>  array(
                0 => '   _____  ',
                1 => '  / ___// ',
                2 => '  \\___ \\\\ ',
                3 => '  /  $ // ',
                4 => ' /____//  ',
                5 => '`-----`   ',
                6 => '          ',
            ),
            84 =>  array(
                0 => '  ______  ',
                1 => ' /_   _// ',
                2 => ' `-| |,-  ',
                3 => '   | ||   ',
                4 => '   |_||   ',
                5 => '   `-`\'   ',
                6 => '          ',
            ),
            85 =>  array(
                0 => ' _    _   ',
                1 => '| || | || ',
                2 => '| || | || ',
                3 => '| \\\\_/ || ',
                4 => ' \\____//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            86 =>  array(
                0 => '__    __  ',
                1 => '\\ \\\\ / // ',
                2 => ' \\ \\/ //  ',
                3 => '  \\  //   ',
                4 => '   \\//    ',
                5 => '    `     ',
                6 => '          ',
            ),
            87 =>  array(
                0 => ' _    _   ',
                1 => '| |  | || ',
                2 => '| |/\\| || ',
                3 => '|  /\\  || ',
                4 => '|_// \\_|| ',
                5 => '`-`   `-` ',
                6 => '          ',
            ),
            88 =>  array(
                0 => ' __   __  ',
                1 => ' \\ \\\\/ // ',
                2 => '  \\ $ //  ',
                3 => '  / . \\\\  ',
                4 => ' /_//\\_\\\\ ',
                5 => ' `-`  --` ',
                6 => '          ',
            ),
            89 =>  array(
                0 => ' __   __  ',
                1 => ' \\ \\\\/ // ',
                2 => '  \\ ` //  ',
                3 => '   | ||   ',
                4 => '   |_||   ',
                5 => '   `-`\'   ',
                6 => '          ',
            ),
            90 =>  array(
                0 => ' ______   ',
                1 => '|____ //  ',
                2 => '   / //   ',
                3 => '  / //    ',
                4 => ' / //__   ',
                5 => '/______|| ',
                6 => '`------`  ',
            ),
            91 =>  array(
                0 => '[',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            92 =>  array(
                0 => '\\',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            93 =>  array(
                0 => ']',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            94 =>  array(
                0 => '^',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            95 =>  array(
                0 => '_',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            96 =>  array(
                0 => '`',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            97 =>  array(
                0 => '   ___    ',
                1 => '  / _ \\\\  ',
                2 => ' / //\\ \\\\ ',
                3 => '|  ___  ||',
                4 => '|_||  |_||',
                5 => '`-`   `-` ',
                6 => '          ',
            ),
            98 =>  array(
                0 => ' ______   ',
                1 => '|      \\\\ ',
                2 => '|  --$ // ',
                3 => '|  --  \\\\ ',
                4 => '|______// ',
                5 => '`------`  ',
                6 => '          ',
            ),
            99 =>  array(
                0 => '  _____   ',
                1 => ' / ____|| ',
                2 => '/ //---`\' ',
                3 => '\\ \\\\___   ',
                4 => ' \\_____|| ',
                5 => '  `----`  ',
                6 => '          ',
            ),
            100 =>  array(
                0 => ' _____    ',
                1 => '|  __ \\\\  ',
                2 => '| |$ \\ || ',
                3 => '| |__/ || ',
                4 => '|_____//  ',
                5 => ' -----`   ',
                6 => '          ',
            ),
            101 =>  array(
                0 => '  _____   ',
                1 => ' |  ___|| ',
                2 => ' | ||__   ',
                3 => ' | ||__   ',
                4 => ' |_____|| ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            102 =>  array(
                0 => '  ______  ',
                1 => ' /_____// ',
                2 => ' `____ `  ',
                3 => ' /___//   ',
                4 => ' `__ `    ',
                5 => ' /_//     ',
                6 => ' `-`      ',
            ),
            103 =>  array(
                0 => '  _____   ',
                1 => ' /  ___|| ',
                2 => '| //$__   ',
                3 => '| \\\\_\\ || ',
                4 => ' \\____//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            104 =>  array(
                0 => ' __   _   ',
                1 => '| || | || ',
                2 => '| \'--\' || ',
                3 => '| .--. || ',
                4 => '|_|| |_|| ',
                5 => '`-`  `-`  ',
                6 => '          ',
            ),
            105 =>  array(
                0 => '  ______  ',
                1 => ' /_   _// ',
                2 => '  -| ||-  ',
                3 => '  _| ||_  ',
                4 => ' /_____// ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            106 =>  array(
                0 => '  ______  ',
                1 => ' /_   _// ',
                2 => '   | ||   ',
                3 => '  _| ||   ',
                4 => ' /__//    ',
                5 => ' `--`     ',
                6 => '          ',
            ),
            107 =>  array(
                0 => '  _  __  ',
                1 => ' | |/ // ',
                2 => ' | \' //  ',
                3 => ' | . \\\\  ',
                4 => ' |_|\\_\\\\ ',
                5 => ' `-` --` ',
                6 => '         ',
            ),
            108 =>  array(
                0 => '  __     ',
                1 => ' | ||    ',
                2 => ' | ||    ',
                3 => ' | ||__  ',
                4 => ' |____// ',
                5 => ' `----`  ',
                6 => '         ',
            ),
            109 =>  array(
                0 => ' _    _   ',
                1 => '| \\  / || ',
                2 => '|  \\/  || ',
                3 => '| .  . || ',
                4 => '|_|\\/|_|| ',
                5 => '`-`  `-`  ',
                6 => '          ',
            ),
            110 =>  array(
                0 => '  _  _   ',
                1 => ' | \\| || ',
                2 => ' |  \' || ',
                3 => ' | .  || ',
                4 => ' |_|\\_|| ',
                5 => ' `-` -`  ',
                6 => '         ',
            ),
            111 =>  array(
                0 => '   ___    ',
                1 => '  / _ \\\\  ',
                2 => ' | /$\\ || ',
                3 => ' | \\_/ || ',
                4 => '  \\___//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            112 =>  array(
                0 => '          ',
                1 => '  ____    ',
                2 => ' |    \\\\  ',
                3 => ' | [] ||  ',
                4 => ' |  __//  ',
                5 => ' |_|`-`   ',
                6 => ' `-`      ',
            ),
            113 =>  array(
                0 => '          ',
                1 => '    ___   ',
                2 => '   /   || ',
                3 => '  | [] || ',
                4 => '   \\__ || ',
                5 => '    -|_|| ',
                6 => '     `-`  ',
            ),
            114 =>  array(
                0 => '  ____    ',
                1 => ' |  _ \\\\  ',
                2 => ' | |_| || ',
                3 => ' | .  //  ',
                4 => ' |_|\\_\\\\  ',
                5 => ' `-` --`  ',
                6 => '          ',
            ),
            115 =>  array(
                0 => '   _____  ',
                1 => '  / ___// ',
                2 => '  \\___ \\\\ ',
                3 => '  /  $ // ',
                4 => ' /____//  ',
                5 => '`-----`   ',
                6 => '          ',
            ),
            116 =>  array(
                0 => '  ______  ',
                1 => ' /_   _// ',
                2 => ' `-| |,-  ',
                3 => '   | ||   ',
                4 => '   |_||   ',
                5 => '   `-`\'   ',
                6 => '          ',
            ),
            117 =>  array(
                0 => ' _    _   ',
                1 => '| || | || ',
                2 => '| || | || ',
                3 => '| \\\\_/ || ',
                4 => ' \\____//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            118 =>  array(
                0 => '__    __  ',
                1 => '\\ \\\\ / // ',
                2 => ' \\ \\/ //  ',
                3 => '  \\  //   ',
                4 => '   \\//    ',
                5 => '    `     ',
                6 => '          ',
            ),
            119 =>  array(
                0 => ' _    _   ',
                1 => '| |  | || ',
                2 => '| |/\\| || ',
                3 => '|  /\\  || ',
                4 => '|_// \\_|| ',
                5 => '`-`   `-` ',
                6 => '          ',
            ),
            120 =>  array(
                0 => ' __   __  ',
                1 => ' \\ \\\\/ // ',
                2 => '  \\ $ //  ',
                3 => '  / . \\\\  ',
                4 => ' /_//\\_\\\\ ',
                5 => ' `-`  --` ',
                6 => '          ',
            ),
            121 =>  array(
                0 => ' __   __  ',
                1 => ' \\ \\\\/ // ',
                2 => '  \\ ` //  ',
                3 => '   | ||   ',
                4 => '   |_||   ',
                5 => '   `-`\'   ',
                6 => '          ',
            ),
            122 =>  array(
                0 => '  _____   ',
                1 => ' |__  //  ',
                2 => '   / //   ',
                3 => '  / //__  ',
                4 => ' /_____|| ',
                5 => ' `-----`  ',
                6 => '          ',
            ),
            123 =>  array(
                0 => '{',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            124 =>  array(
                0 => '|',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            125 =>  array(
                0 => '}',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            126 =>  array(
                0 => '~',
                1 => ' ',
                2 => ' ',
                3 => ' ',
                4 => ' ',
                5 => ' ',
                6 => ' ',
            ),
            196 =>  array(
                0 => '  []|_[]| ',
                1 => '  / _ \\\\  ',
                2 => ' / //\\ \\\\ ',
                3 => '| $___$ ||',
                4 => '|_||$ |_||',
                5 => '`-`   `-` ',
                6 => '          ',
            ),
            214 =>  array(
                0 => '  []|_[]| ',
                1 => '  / _ \\\\  ',
                2 => ' | /$\\ || ',
                3 => ' | \\_/ || ',
                4 => '  \\___//  ',
                5 => '   ---`   ',
                6 => '          ',
            ),
            220 =>  array(
                0 => ' []| []|  ',
                1 => '| ||$| || ',
                2 => '| ||$| || ',
                3 => '| \\\\_/ || ',
                4 => ' \\____//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            228 =>  array(
                0 => '  []|_[]| ',
                1 => '  / _ \\\\  ',
                2 => ' / //\\ \\\\ ',
                3 => '| $___$ ||',
                4 => '|_||$ |_||',
                5 => '`-`   `-` ',
                6 => '          ',
            ),
            246 =>  array(
                0 => '  []|_[]| ',
                1 => '  / _ \\\\  ',
                2 => ' | /$\\ || ',
                3 => ' | \\_/ || ',
                4 => '  \\___//  ',
                5 => '   ---`   ',
                6 => '          ',
            ),
            252 =>  array(
                0 => ' []| []|  ',
                1 => '| ||$| || ',
                2 => '| ||$| || ',
                3 => '| \\\\_/ || ',
                4 => ' \\____//  ',
                5 => '  `---`   ',
                6 => '          ',
            ),
            223 =>  array(
                0 => '  ,--.    ',
                1 => ' | _$ \\\\  ',
                2 => ' |    //  ',
                3 => ' | |\\ \\\\  ',
                4 => ' |$ ___\\\\ ',
                5 => ' |_|----` ',
                6 => '  -       ',
            ),
            162 =>  array(
                0 => '   _    ',
                1 => '  | ||  ',
                2 => ' / __// ',
                3 => '| (__`  ',
                4 => ' \\   \\\\ ',
                5 => '  |_|`  ',
                6 => '  `-`  ',
            ),
            215 =>  array(
                0 => '      ',
                1 => '      ',
                2 => ' \\\\// ',
                3 => '  \\\\  ',
                4 => ' //\\\\ ',
                5 => '      ',
                6 => '      ',
            ),
        );
    }
}
