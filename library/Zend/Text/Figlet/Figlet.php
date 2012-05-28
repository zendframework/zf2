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

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Zend\Text\Figlet is a ZF implementation of FIGlet
 *
 * @category  Zend
 * @package   Zend_Text_Figlet
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Figlet
{
    /**
     * Options
     *
     * @var FigletOptions
     */
    protected $options = null;

    /**
     * Currenty used font
     *
     * @var Font
     */
    protected $font = null;

    /**
     * @var integer
     */
    protected $outputWidth;

    /**
     * Height of the characters
     *
     * @var integer
     */
    protected $charHeight;

    /**
     * Print direction
     *
     * @var integer
     */
    protected $direction;


    /**
     * Smushing mode
     *
     * @var integer
     */
    protected $smushMode;

    /**
     * Max length of any character
     *
     * @var integer
     */
    protected $maxLength;

    /**
     * Previous character width
     *
     * @var integer
     */
    protected $previousCharWidth = 0;

    /**
     * Current character width
     *
     * @var integer
     */
    protected $currentCharWidth = 0;

    /**
     * Current outline length
     *
     * @var integer
     */
    protected $outlineLength = 0;

    /**
     * Maxmimum outline length
     *
     * @var integer
     */
    protected $outlineLengthLimit = 0;

    /**
     * In character line
     *
     * @var string
     */
    protected $inCharLine;

    /**
     * In character line length
     *
     * @var integer
     */
    protected $inCharLineLength = 0;

    /**
     * Maximum in character line length
     *
     * @var integer
     */
    protected $inCharLineLengthLimit = 0;

    /**
     * Current char
     *
     * @var array
     */
    protected $currentChar = null;

    /**
     * Current output line
     *
     * @var array
     */
    protected $outputLine;

    /**
     * Current output
     *
     * @var string
     */
    protected $output;

    /**
     * Instantiate the FIGlet with a specific font. If no font is given, the
     * standard font is used. You can also supply multiple options via
     * the $options variable, which can either be an array or an instance of
     * Zend_Config.
     *
     * @param FigletOptions $options Options for the output
     */
    public function __construct(FigletOptions $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options
     *
     * @param FigletOptions $options
     * @return Figlet
     */
    public function setOptions(FigletOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get options
     *
     * @return FigletOptions
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->options = new FigletOptions();
        }

        return $this->options;
    }

    /**
     * Render a FIGlet text
     *
     * @param  string $text     Text to convert to a figlet text
     * @param  string $encoding Encoding of the input string
     * @return string
     * @throws Exception\InvalidArgumentException When $text is not a string
     * @throws Exception\UnexpectedValueException When $text is not encoded
     *                                            as $encoding
     */
    public function render($text, $encoding = 'UTF-8')
    {
        if (!is_string($text)) {
            throw new Exception\InvalidArgumentException('$text must be a string');
        }

        $this->init();

        if ($encoding !== 'UTF-8') {
            $text = iconv($encoding, 'UTF-8', $text);
        }

        $this->output     = '';
        $this->outputLine = array();

        $this->clearLine();

        $this->outlineLengthLimit    = ($this->outputWidth - 1);
        $this->inCharLineLengthLimit = ($this->outputWidth * 4 + 100);

        $wordBreakMode  = 0;
        $lastCharWasEol = false;
        $textLength     = @iconv_strlen($text, 'UTF-8');

        if ($textLength === false) {
            throw new Exception\UnexpectedValueException('$text is not encoded with ' . $encoding);
        }

        for ($charNum = 0; $charNum < $textLength; $charNum++) {
            // Handle paragraphs
            $char = iconv_substr($text, $charNum, 1, 'UTF-8');

            if ($char === "\n" && $this->options->getHandleParagraphs() && !$lastCharWasEol) {
                $nextChar = iconv_substr($text, ($charNum + 1), 1, 'UTF-8');
                if (!$nextChar) {
                    $nextChar = null;
                }

                $char = (ctype_space($nextChar)) ? "\n" : ' ';
            }

            $lastCharWasEol = (ctype_space($char) && $char !== "\t" && $char !== ' ');

            if (ctype_space($char)) {
                $char = ($char === "\t" || $char === ' ') ? ' ': "\n";
            }

            // Skip unprintable characters
            $ordChar = $this->uniOrd($char);
            if (($ordChar > 0 && $ordChar < 32 && $char !== "\n") || $ordChar === 127) {
                continue;
            }

            // Build the character
            // Note: The following code is complex and thoroughly tested.
            // Be careful when modifying!
            do {
                $charNotAdded = false;

                if ($wordBreakMode === -1) {
                    if ($char === ' ') {
                        break;
                    } elseif ($char === "\n") {
                        $wordBreakMode = 0;
                        break;
                    }
                    $wordBreakMode = 0;
                }

                if ($char === "\n") {
                    $this->appendLine();
                    $wordBreakMode = false;
                } elseif ($this->addChar($char)) {
                    if ($char !== ' ') {
                        $wordBreakMode = ($wordBreakMode >= 2) ? 3: 1;
                    } else {
                        $wordBreakMode = ($wordBreakMode > 0) ? 2: 0;
                    }
                } elseif ($this->outlineLength === 0) {
                    for ($i = 0; $i < $this->charHeight; $i++) {
                        if ($this->direction === FigletOptions::DIRECTION_RTL && $this->outputWidth > 1) {
                            $offset = (strlen($this->currentChar[$i]) - $this->outlineLengthLimit);
                            $this->putString(substr($this->currentChar[$i], $offset));
                        } else {
                            $this->putString($this->currentChar[$i]);
                        }
                    }

                    $wordBreakMode = -1;
                } elseif ($char === ' ') {
                    if ($wordBreakMode === 2) {
                        $this->splitLine();
                    } else {
                        $this->appendLine();
                    }

                    $wordBreakMode = -1;
                } else {
                    if ($wordBreakMode >= 2) {
                        $this->splitLine();
                    } else {
                        $this->appendLine();
                    }

                    $wordBreakMode = ($wordBreakMode === 3) ? 1 : 0;
                    $charNotAdded  = true;
                }
            } while ($charNotAdded);
        }

        if ($this->outlineLength !== 0) {
            $this->appendLine();
        }

        return $this->output;
    }

    /**
     * Init all variables before rendering
     */
    protected function init()
    {
        $options           = $this->getOptions();
        $this->font        = $options->getFont();
        $this->outputWidth = $options->getOutputWidth();
        $this->direction   = $options->getDirection();
        $this->smushMode   = $options->getSmushMode();
        $this->charHeight  = max(1, $this->font->getParam('height'));
        $this->maxLength   = max(1, $this->font->getParam('max_length'));

        // Give ourselves some extra room
        $this->maxLength += 100;
    }

    /**
     * Puts the given string, substituting blanks for hardblanks.
     * If outputWidth is 1, puts the entire string;
     * otherwise puts at most outputWidth - 1 characters.
     * Puts a newline at the end of the string. The string is left-aligned,
     * centered or right-aligned (taking outputWidth as the screen width)
     * if alignment is 0, 1 or 2 respectively.
     *
     * @param  string $string The string to add to the output
     * @return void
     */
    protected function putString($string)
    {
        $length = strlen($string);

        if ($this->outputWidth > 1) {
            if ($length > ($this->outputWidth - 1)) {
                $length = ($this->outputWidth - 1);
            }

            $align = $this->options->getAlign();

            if ($align > FigletOptions::ALIGN_LEFT) {
                for ($i = 1;
                     ((3 - $align) * $i + $length + $align - 2) < $this->outputWidth;
                     $i++) {
                    $this->output .= ' ';
                }
            }
        }

        $this->output .= str_replace($this->font->getHardBlank(), ' ', $string) . "\n";
    }

    /**
     * Appends the current line to the output
     *
     * @return void
     */
    protected function appendLine()
    {
        for ($i = 0; $i < $this->charHeight; $i++) {
            $this->putString($this->outputLine[$i]);
        }

        $this->clearLine();
    }

    /**
     * Splits inCharLine at the last word break (bunch of consecutive blanks).
     * Makes a new line out of the first part and appends it using appendLine().
     * Makes a new line out of the second part and returns.
     *
     * @return void
     */
    protected function splitLine()
    {
        $gotSpace  = false;
        $lastSpace = 0;
        for ($i = ($this->inCharLineLength - 1); $i >= 0; $i--) {
            if (!$gotSpace && $this->inCharLine[$i] === ' ') {
                $gotSpace  = true;
                $lastSpace = $i;
            }

            if ($gotSpace && $this->inCharLine[$i] !== ' ') {
                break;
            }
        }

        $firstLength = ($i + 1);
        $lastLength  = ($this->inCharLineLength - $lastSpace - 1);

        $firstPart = '';
        for ($i = 0; $i < $firstLength; $i++) {
            $firstPart[$i] = $this->inCharLine[$i];
        }

        $lastPart = '';
        for ($i = 0; $i < $lastLength; $i++) {
            $lastPart[$i] = $this->inCharLine[($lastSpace + 1 + $i)];
        }

        $this->clearLine();

        for ($i = 0; $i < $firstLength; $i++) {
            $this->addChar($firstPart[$i]);
        }

        $this->appendLine();

        for ($i = 0; $i < $lastLength; $i++) {
            $this->addChar($lastPart[$i]);
        }
    }

    /**
     * Clears the current line
     *
     * @return void
     */
    protected function clearLine()
    {
        for ($i = 0; $i < $this->charHeight; $i++) {
            $this->outputLine[$i] = '';
        }

        $this->outlineLength    = 0;
        $this->inCharLineLength = 0;
    }

    /**
     * Attempts to add the given character onto the end of the current line.
     * Returns true if this can be done, false otherwise.
     *
     * @param  string $char Character which to add to the output
     * @return boolean
     */
    protected function addChar($char)
    {
        $this->getLetter($char);

        if ($this->currentChar === null) {
            return true;
        }

        $smushAmount = $this->smushAmount();

        if (($this->outlineLength + $this->currentCharWidth - $smushAmount) > $this->outlineLengthLimit
            || ($this->inCharLineLength + 1) > $this->inCharLineLengthLimit) {
            return false;
        }

        $tempLine = '';
        for ($row = 0; $row < $this->charHeight; $row++) {
            if ($this->direction === FigletOptions::DIRECTION_RTL) {
                $tempLine = $this->currentChar[$row];

                for ($k = 0; $k < $smushAmount; $k++) {
                    $position            = ($this->currentCharWidth - $smushAmount + $k);
                    $tempLine[$position] = $this->smushEm($tempLine[$position], $this->outputLine[$row][$k]);
                }

                $this->outputLine[$row] = $tempLine . substr($this->outputLine[$row], $smushAmount);
            } else {
                for ($k = 0; $k < $smushAmount; $k++) {
                    if (($this->outlineLength - $smushAmount + $k) < 0) {
                        continue;
                    }

                    $position = ($this->outlineLength - $smushAmount + $k);
                    if (isset($this->outputLine[$row][$position])) {
                        $leftChar = $this->outputLine[$row][$position];
                    } else {
                        $leftChar = null;
                    }

                    $this->outputLine[$row][$position] = $this->smushEm($leftChar, $this->currentChar[$row][$k]);
                }

                $this->outputLine[$row] .= substr($this->currentChar[$row], $smushAmount);
            }
        }

        $this->outlineLength                         = strlen($this->outputLine[0]);
        $this->inCharLine[$this->inCharLineLength++] = $char;

        return true;
    }

    /**
     * Gets the requested character and sets current and previous char width.
     *
     * @param  string $char The character from which to get the letter of
     * @return void
     */
    protected function getLetter($char)
    {
        $code              = $this->uniOrd($char);
        $this->currentChar = $this->font->getChar($code);

        if ($this->currentChar !== null) {
            $this->previousCharWidth = $this->currentCharWidth;
            $this->currentCharWidth  = strlen($this->currentChar[0]);
        }
    }

    /**
     * Returns the maximum amount that the current character can be smushed into
     * the current line.
     *
     * @return integer
     */
    protected function smushAmount()
    {
        if (($this->smushMode & (FigletOptions::SM_SMUSH | FigletOptions::SM_KERN)) === 0) {
            return 0;
        }

        $maxSmush = $this->currentCharWidth;
        $amount   = $maxSmush;

        for ($row = 0; $row < $this->charHeight; $row++) {
            if ($this->direction === FigletOptions::DIRECTION_RTL) {
                $charbd = strlen($this->currentChar[$row]);
                while (true) {
                    if (!isset($this->currentChar[$row][$charbd])) {
                        $leftChar = null;
                    } else {
                        $leftChar = $this->currentChar[$row][$charbd];
                    }

                    if ($charbd > 0 && ($leftChar === null || $leftChar == ' ')) {
                        $charbd--;
                    } else {
                        break;
                    }
                }

                $linebd = 0;
                while (true) {
                    if (!isset($this->outputLine[$row][$linebd])) {
                        $rightChar = null;
                    } else {
                        $rightChar = $this->outputLine[$row][$linebd];
                    }

                    if ($rightChar === ' ') {
                        $linebd++;
                    } else {
                        break;
                    }
                }

                $amount = ($linebd + $this->currentCharWidth - 1 - $charbd);
            } else {
                $linebd = strlen($this->outputLine[$row]);
                while (true) {
                    if (!isset($this->outputLine[$row][$linebd])) {
                        $leftChar = null;
                    } else {
                        $leftChar = $this->outputLine[$row][$linebd];
                    }

                    if ($linebd > 0 && ($leftChar === null || $leftChar == ' ')) {
                        $linebd--;
                    } else {
                        break;
                    }
                }

                $charbd = 0;
                while (true) {
                    if (!isset($this->currentChar[$row][$charbd])) {
                        $rightChar = null;
                    } else {
                        $rightChar = $this->currentChar[$row][$charbd];
                    }

                    if ($rightChar === ' ') {
                        $charbd++;
                    } else {
                        break;
                    }
                }

                $amount = ($charbd + $this->outlineLength - 1 - $linebd);
            }

            if (empty($leftChar) || $leftChar === ' ') {
                $amount++;
            } else if (!empty($rightChar)) {
                if ($this->smushEm($leftChar, $rightChar) !== null) {
                    $amount++;
                }
            }

            $maxSmush = min($amount, $maxSmush);
        }

        return $maxSmush;
    }

    /**
     * Given two characters, attempts to smush them into one, according to the
     * current smushmode. Returns smushed character or false if no smushing can
     * be done.
     *
     * Smushmode values are sum of following (all values smush blanks):
     *
     *  1: Smush equal chars (not hardblanks)
     *  2: Smush '_' with any char in hierarchy below
     *  4: hierarchy: "|", "/\", "[]", "{}", "()", "<>"
     *     Each class in hier. can be replaced by later class.
     *  8: [ + ] -> |, { + } -> |, ( + ) -> |
     * 16: / + \ -> X, > + < -> X (only in that order)
     * 32: hardblank + hardblank -> hardblank
     *
     * @param  string $leftChar  Left character to smush
     * @param  string $rightChar Right character to smush
     * @return string
     */
    protected function smushEm($leftChar, $rightChar)
    {
        if ($leftChar === ' ') {
            return $rightChar;
        }

        if ($rightChar === ' ') {
            return $leftChar;
        }

        if ($this->previousCharWidth < 2 || $this->currentCharWidth < 2) {
            // Disallows overlapping if the previous character or the current
            // character has a width of one or zero.
            return null;
        }

        if (($this->smushMode & FigletOptions::SM_SMUSH) === 0) {
            // Kerning
            return null;
        }

        $hardBlank = $this->font->getHardBlank();

        if (($this->smushMode & 63) === 0) {
            // This is smushing by universal overlapping
            if ($leftChar === ' ') {
                return $rightChar;
            } else if ($rightChar === ' ') {
                return $leftChar;
            } else if ($leftChar === $hardBlank) {
                return $rightChar;
            } else if ($rightChar === $hardBlank) {
                return $rightChar;
            } elseif ($this->direction === FigletOptions::DIRECTION_RTL) {
                return $leftChar;
            } else {
                // Occurs in the absence of above exceptions
                return $rightChar;
            }
        }

        if (($this->smushMode & FigletOptions::SM_HARDBLANK) > 0) {
            if ($leftChar === $hardBlank && $rightChar === $hardBlank) {
                return $leftChar;
            }
        }

        if ($leftChar === $hardBlank && $rightChar === $hardBlank) {
            return null;
        }

        if (($this->smushMode & FigletOptions::SM_EQUAL) > 0) {
            if ($leftChar === $rightChar) {
                return $leftChar;
            }
        }

        if (($this->smushMode & FigletOptions::SM_LOWLINE) > 0) {
            if ($leftChar === '_' && strchr('|/\\[]{}()<>', $rightChar) !== false) {
                return $rightChar;
            } elseif ($rightChar === '_' && strchr('|/\\[]{}()<>', $leftChar) !== false) {
                return $leftChar;
            }
        }

        if (($this->smushMode & FigletOptions::SM_HIERARCHY) > 0) {
            if ($leftChar === '|' && strchr('/\\[]{}()<>', $rightChar) !== false) {
                return $rightChar;
            } elseif ($rightChar === '|' && strchr('/\\[]{}()<>', $leftChar) !== false) {
                return $leftChar;
            } elseif (strchr('/\\', $leftChar) && strchr('[]{}()<>', $rightChar) !== false) {
                return $rightChar;
            } elseif (strchr('/\\', $rightChar) && strchr('[]{}()<>', $leftChar) !== false) {
                return $leftChar;
            } elseif (strchr('[]', $leftChar) && strchr('{}()<>', $rightChar) !== false) {
                return $rightChar;
            } elseif (strchr('[]', $rightChar) && strchr('{}()<>', $leftChar) !== false) {
                return $leftChar;
            } elseif (strchr('{}', $leftChar) && strchr('()<>', $rightChar) !== false) {
                return $rightChar;
            } elseif (strchr('{}', $rightChar) && strchr('()<>', $leftChar) !== false) {
                return $leftChar;
            } elseif (strchr('()', $leftChar) && strchr('<>', $rightChar) !== false) {
                return $rightChar;
            } elseif (strchr('()', $rightChar) && strchr('<>', $leftChar) !== false) {
                return $leftChar;
            }
        }

        if (($this->smushMode & FigletOptions::SM_PAIR) > 0) {
            if ($leftChar === '[' && $rightChar === ']') {
                return '|';
            } elseif ($rightChar === '[' && $leftChar === ']') {
                return '|';
            } elseif ($leftChar === '{' && $rightChar === '}') {
                return '|';
            } elseif ($rightChar === '{' && $leftChar === '}') {
                return '|';
            } elseif ($leftChar === '(' && $rightChar === ')') {
                return '|';
            } elseif ($rightChar === '(' && $leftChar === ')') {
                return '|';
            }
        }

        if (($this->smushMode & FigletOptions::SM_BIGX) > 0) {
            if ($leftChar === '/' && $rightChar === '\\') {
                return '|';
            } elseif ($rightChar === '/' && $leftChar === '\\') {
                return 'Y';
            } elseif ($leftChar === '>' && $rightChar === '<') {
                return 'X';
            }
        }

        return null;
    }

    /**
     * Unicode compatible ord() method
     *
     * @param  string $c The char to get the value from
     * @return integer
     */
    protected function uniOrd($c)
    {
        $h = ord($c[0]);

        if ($h <= 0x7F) {
            $ord = $h;
        } else if ($h < 0xC2) {
            $ord = 0;
        } else if ($h <= 0xDF) {
            $ord = (($h & 0x1F) << 6 | (ord($c[1]) & 0x3F));
        } else if ($h <= 0xEF) {
            $ord = (($h & 0x0F) << 12 | (ord($c[1]) & 0x3F) << 6 | (ord($c[2]) & 0x3F));
        } else if ($h <= 0xF4) {
            $ord = (($h & 0x0F) << 18 | (ord($c[1]) & 0x3F) << 12 |
                   (ord($c[2]) & 0x3F) << 6 | (ord($c[3]) & 0x3F));
        } else {
            $ord = 0;
        }

        return $ord;
    }
}