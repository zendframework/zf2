#!/usr/bin/env php
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendBin;

use Zend\Console;
use Zend\File\ClassFileLocator;
use Zend\Loader\StandardAutoloader;
use Zend\Text\Table;

/*
 * Convert a file from DocBook to reStructuredText format
 * 
 * Note: to convert the ZF documentation you have to use the
 * --normalize parameter. This parameter will convert the file name and
 * the internal links of the document using the ZF naming convention.
 * 
 * Usage:
 * --help|-h        Get usage message
 * --docbook|-d     Docbook file to convert
 * --output|-o      Output file, by default assumes <docbook>.rst
 * --normalize|-n   Normalize the file name and the internal links using
 *                  the ZF naming convention
 */
echo "DocBook to reStructuredText conversion\n";
echo "--------------------------------------\n";

$libPath = getenv('LIB_PATH') ? getenv('LIB_PATH') : __DIR__ . '/../library';
if (!is_dir($libPath)) {
    // Try to load StandardAutoloader from include_path
    if (false === include('Zend/Loader/StandardAutoloader.php')) {
        echo "Unable to locate autoloader via include_path; aborting" . PHP_EOL;
        exit(2);
    }
} elseif (false === include($libPath . '/Zend/Loader/StandardAutoloader.php')) {
    echo "Unable to locate autoloader via library; aborting" . PHP_EOL;
    exit(2);
}

// Setup autoloading
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

$rules = array(
    'help|h'      => 'Get usage message',
    'docbook|d-s' => 'Docbook file to convert',
    'output|o-s'  => 'Output file, by default assumes <docbook>.rst',
    'normalize|n' => 'Normalize the file name and the internal links using ' .
                     'the ZF naming convention'
);

try {
    $opts = new Console\Getopt($rules);
    $opts->parse();
} catch (Console\Exception\RuntimeException $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (!$opts->getOptions() || $opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit(0);
}

$docbook = $opts->getOption('d');
if (!file_exists($docbook)) {
    echo "Error: the docbook file $docbook doesn't exist." . PHP_EOL;
    exit(2);
}

$normalize = (null !== $opts->getOption('n'));

$rstFile = $opts->getOption('o');
if (empty($rstFile)) {
    $rstFile = basename($docbook);
    if ('.xml' === substr($rstFile, -4)) {
        $rstFile = substr($rstFile, 0, strlen($docbook)-4);
    }
    if ($normalize) {
        $rstFile = RstConvert::xmlFileNameToRst($rstFile);
    }
    if ('.rst' !== substr($rstFile, -4)) {
        $rstFile .= '.rst';
    }
    $rstFile = dirname($docbook) . DIRECTORY_SEPARATOR . $rstFile;
}

// Load the docbook file (input)
$xml = new \DOMDocument;
$xml->load($docbook);

$xsltFile = __DIR__ . '/doc2rst.xsl';

// Load the XSLT file
$xsl = new \DOMDocument;
if (!file_exists($xsltFile)) {
    echo "The $xsltFile is missing, I cannot proceed with the conversion." . PHP_EOL;
    exit(2);
}
$xsl->load($xsltFile);

$proc = new \XSLTProcessor;
$proc->registerPHPFunctions();
$proc->setParameter('','normalize',(int) $normalize);
$proc->importStyleSheet($xsl);

echo "Writing to $rstFile\n";

$output = $proc->transformToXml($xml);
if (false === $output) {
    echo 'Error during the conversion' . PHP_EOL;
    exit(2);
}

// remove single spaces in the beginning of new lines
$lines  = explode("\n", $output);
$output = '';
foreach ($lines as $line) {
    if ($line === '') {
        $output .= "\n";
    } elseif (($line[0] === ' ') && ($line[1] !== ' ')) {
        $output .= substr($line, 1) . "\n";
    } else {
        // Remove trailing spaces
        $output .= rtrim($line). "\n";
    }
}
// Add the list of the external links at the end of the document
if(!empty(RstConvert::$links)) {
    $output .= "\n" . RstConvert::getLinks();
}
if(!empty(RstConvert::$footnote)) {
    $output .= "\n" . join("\n", RstConvert::$footnote);
}

file_put_contents($rstFile, $output);
echo 'Conversion done.' . PHP_EOL;
exit(0);

/**
 * XSLT php:function()
 */
class RstConvert
{
    public static $links    = array();
    public static $footnote = array();

    /**
     * Indent the text 3 spaces by default
     *
     * @param  string $text
     * @return string
     */
    public static function indent($text)
    {
        $rows   = explode("\n", $text);
        $output = '';
        foreach ($rows as $row) {
            if ($row === '' || preg_match('/^\s+$/', $row)) {
                $output .= "\n";
            } else {
                $output .= "   $row\n";
            }
        }
        return $output;
    }

    /**
     * Indent the text 2 spaces
     *
     * @param  string $text
     * @return string
     */
    public static function indent2($text)
    {
        $rows   = explode("\n", $text);
        $output = '';
        foreach ($rows as $row) {
            if ($row === '' || preg_match('/^\s+$/', $row)) {
                $output .= "\n";
            } else {
                $output .= "  $row\n";
            }
        }
        return $output;
    }

    /**
     * Indent the text 7 spaces
     *
     * @param  string $text
     * @return string
     */
    public static function indent7($text)
    {
        $rows   = explode("\n", $text);
        $output = '';
        foreach ($rows as $row) {
            if ($row === '' || preg_match('/^\s+$/', $row)) {
                $output .= "\n";
            } else {
                $output .= "       $row\n";
            }
        }
        return $output;
    }

    /**
     * Convert all the section/title, except for the first
     *
     * @param  string $text
     * @param  string $sign decorator character by default paragraph character
     * @param  bool   $top  put decorator line on top too
     * @return string
     */
    public static function title($text, $sign = '-', $top = false)
    {
        $text   = trim(self::formatText($text));
        $line   = str_repeat($sign, strlen($text));
        $output = $text . "\n" . $line . "\n";
        if ($top) {
            $output = $line . "\n" . $output;
        }
        return $output . "\n";
    }

    /**
     * Format the string removing \n, multiple white spaces and \ in \\
     *
     * @param  string         $text
     * @param  \DOMDocument[] $preceding previously sibling
     * @param  \DOMDocument[] $following following sibling
     * @return string
     */
    public static function formatText($text, $preceding = false, $following = false)
    {
        $hasPreceding = !empty($preceding);
        $hasFollowing = !empty($following);
        $escaped = self::escapeChars(trim(preg_replace('/\s+/m', ' ', $text)));

        if ($hasPreceding) {
            if (!in_array($escaped[0],
                          array('-', '.', ',', ':', ';', '!', '?', '\\', '/', "'", '"', ')', ']', '}', '>', ' '))
            ) {
                $escaped = ' ' . $escaped;
                if (preg_match('/[^\s]/', $text[0])) {
                    $escaped = '\\' . $escaped;
                }
            }
        } else {
            // Escape characters in the bullet list or format character
            if (preg_match('/^([-\+•‣⁃]($|\s)|[_`\*\|])/', $escaped)) {
                $escaped = '\\' . $escaped;
            }
        }

        if ($hasFollowing) {
            if ($following[0]->localName == 'superscript') {
                $escaped .= '\ ';
            } elseif (!in_array(substr($escaped, -1), array('-', '/', "'", '"', '(', '[', '{', '<', ' '))) {
                // Omitted  ':' in the list
                $escaped .= ' ';
            }
        }
        return $escaped;
    }

    /**
     * Escape chars
     *
     * @param  string $text
     * @return string
     */
    public static function escapeChars($text)
    {
        // Exclude special character if preceded by any valid preceded character
        return preg_replace('/((([-:\/\'"\(\[\{<\s])([_`\*\|][^\s]))|([_][-\.,:;!?\/\\\'"\)\]\}>\s]))/S', '$3\\\$4$5',
                            str_replace('\\', '\\\\', $text));
    }

    /**
     * Escape an specific char
     *
     * @param  string $text
     * @param  string $char Char to escape
     * @return string
     */
    public static function escapeChar($text, $char)
    {
        return preg_replace(sprintf('/([^\s])(\\%s[^-\.,:;!?\/\\\'"\)\]\}>\s])/', $char), '$1\\\$2', $text);
    }

    /**
     * Convert the link tag
     *
     * @param  \DOMElement $node
     * @return string
     */
    public static function link($node)
    {
        $value = trim(self::formatText($node[0]->nodeValue));
        if ($node[0]->getAttribute('linkend')) {
            return ":ref:`$value <" . $node[0]->getAttribute('linkend') . ">`";
        } else {
            self::$links[$value] = trim($node[0]->getAttribute('xlink:href'));
            return "`$value`_";
        }
    }

    /**
     * Convert the footnote
     *
     * @param  \DOMElement $value
     * @return string
     */
    public static function footnote($value)
    {
        self::$footnote[] = '.. [#] ' . trim($value);
        return '[#]_';
    }

    /**
     * Get all the external links of the document
     *
     * @return string
     */
    public static function getLinks()
    {
        $output = '';
        foreach (self::$links as $key => $value) {
            $output .= ".. _`$key`: $value\n";
        }
        return $output;
    }

    /**
     * Convert the table tag
     *
     * @param  \DOMElement $node
     * @return string
     */
    public static function table($node)
    {
        // check if thead exists
        if (0 !== $node[0]->getElementsByTagName('thead')->length) {
            $head = true;
        } else {
            $head = false;
        }
        $rows   = $node[0]->getElementsByTagName('row');
        $table  = array();
        $totRow = $rows->length;
        $j      = 0;
        foreach ($rows as $row) {
            $cols   = $row->getElementsByTagName('entry');
            $totCol = $cols->length;
            if (!isset($widthCol)) {
                $widthCol = array_fill(0, $totCol, 0);
            }
            $i = 0;
            foreach ($cols as $col) {
                $table[$j][$i] = self::formatText($col->nodeValue);
                $length        = strlen($table[$j][$i]);
                if ($length > $widthCol[$i]) {
                    $widthCol[$i] = $length;
                }
                $i++;
            }
            $j++;
        }
        $tableText = new Table\Table(array(
                                          'columnWidths' => $widthCol,
                                          'decorator'    => 'ascii'
                                     ));
        for ($j = 0; $j < $totRow; $j++) {
            $row = new Table\Row();
            for ($i = 0; $i < $totCol; $i++) {
                $row->appendColumn(new Table\Column($table[$j][$i]));
            }
            $tableText->appendRow($row);
        }
        $output = $tableText->render();
        // if thead exists change the table style with head (= instead of -)
        if ($head) {
            $table     = explode("\n", $output);
            $newOutput = '';
            $i         = 0;
            foreach ($table as $row) {
                if ('+-' === substr($row, 0, 2)) {
                    $i++;
                }
                if (2 === $i) {
                    $row = str_replace('-', '=', $row);
                }
                $newOutput .= "$row\n";
            }
            return $newOutput;
        }
        return $output . "\n";
    }

    /**
     * Convert an XML file name to the RST ZF2 standard naming convention
     * For instance, Zend_Config-XmlIntro.xml become zend.config.xml-intro.rst
     *
     * @param  string $name
     * @return string
     */
    public static function xmlFileNameToRst($name)
    {
        if ('.xml' === strtolower(substr($name, -4))) {
            $name = substr($name, 0, strlen($name) - 4);
        }
        $tot    = strlen($name);
        $output = '';
        $word   = false;
        for ($i = 0; $i < $tot; $i++) {

            if (preg_match('/[A-Z]/', $name[$i])) {
                if ($word) {
                    $output .= '-';
                }
                $output .= strtolower($name[$i]);
            } elseif ('_' === $name[$i] || '-' === $name[$i]) {
                $output .= '.';
                $word = false;
            } else {
                $output .= $name[$i];
                $word = true;
            }
        }
        return $output . '.rst';
    }

    /**
     * Change the image file name to the ../images folder
     *
     * @param  string $href
     * @return string
     */
    public static function imageFileName($href)
    {
        return '../images/' . basename($href);
    }
}
