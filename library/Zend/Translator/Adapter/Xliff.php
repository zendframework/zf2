<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Translator\Adapter;
use Zend\Translator\Adapter\AbstractAdapter,
    Zend\Translator,
    Zend\Translator\Exception\InvalidArgumentException,
    Zend\Translator\Exception\InvalidFileTypeException;

/**
 * @uses       \Zend\Locale\Locale
 * @uses       \Zend\Translator\Adapter\AbstractAdapter
 * @uses       \Zend\Translator\Exception\InvalidArgumentException
 * @uses       \Zend\Translator\Exception\InvalidFileTypeException
 * @category   Zend
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Xliff extends AbstractAdapter
{
    // Internal variables
    private $_file        = false;
    private $_useId       = true;
    private $_cleared     = array();
    private $_transunit   = null;
    private $_source      = null;
    private $_target      = null;
    private $_langId      = null;
    private $_scontent    = null;
    private $_tcontent    = null;
    private $_stag        = false;
    private $_ttag        = false;
    private $_data        = array();

    /**
     * Load translation data (XLIFF file reader)
     *
     * @param  string  $locale    Locale/Language to add data for, identical with locale identifier,
     *                            see Zend_Locale for more information
     * @param  string  $filename  XLIFF file to add, full path must be given for access
     * @param  array   $option    OPTIONAL Options to use
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @throws \Zend\Translator\Exception\InvalidFileTypeException
     * @return array
     */
    protected function _loadTranslationData($filename, $locale, array $options = array())
    {
        $this->_data = array();
        if (!is_readable($filename)) {
            throw new InvalidArgumentException('Translation file \'' . $filename . '\' is not readable.');
        }

        if (empty($options['useId'])) {
            $this->_useId = false;
        } else {
            $this->_useId = true;
        }

        $encoding      = $this->_findEncoding($filename);
        $this->_target = $locale;
        $this->_file   = xml_parser_create($encoding);
        xml_set_object($this->_file, $this);
        xml_parser_set_option($this->_file, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->_file, "_startElement", "_endElement");
        xml_set_character_data_handler($this->_file, "_contentElement");

        if (!xml_parse($this->_file, file_get_contents($filename))) {
            $ex = sprintf('XML error: %s at line %d',
                          xml_error_string(xml_get_error_code($this->_file)),
                          xml_get_current_line_number($this->_file));
            xml_parser_free($this->_file);
            throw new InvalidFileTypeException($ex);
        }

        return $this->_data;
    }

    private function _startElement($file, $name, $attrib)
    {
        if ($this->_stag === true) {
            $this->_scontent .= "<".$name;
            foreach($attrib as $key => $value) {
                $this->_scontent .= " $key=\"$value\"";
            }
            $this->_scontent .= ">";
        } else if ($this->_ttag === true) {
            $this->_tcontent .= "<".$name;
            foreach($attrib as $key => $value) {
                $this->_tcontent .= " $key=\"$value\"";
            }
            $this->_tcontent .= ">";
        } else {
            switch(strtolower($name)) {
                case 'file':
                    $this->_source = $attrib['source-language'];
                    if (isset($attrib['target-language'])) {
                        $this->_target = $attrib['target-language'];
                    }

                    if (!isset($this->_data[$this->_source])) {
                        $this->_data[$this->_source] = array();
                    }

                    if (!isset($this->_data[$this->_target])) {
                        $this->_data[$this->_target] = array();
                    }

                    break;
                case 'trans-unit':
                    $this->_transunit = true;
                    $this->_langId = $attrib['id'];
                    break;
                case 'source':
                    if ($this->_transunit === true) {
                        $this->_scontent = null;
                        $this->_stag = true;
                        $this->_ttag = false;
                    }
                    break;
                case 'target':
                    if ($this->_transunit === true) {
                        $this->_tcontent = null;
                        $this->_ttag = true;
                        $this->_stag = false;
                    }
                    break;
                default:
                    break;
            }
        }
    }

    private function _endElement($file, $name)
    {
        if (($this->_stag === true) and ($name !== 'source')) {
            $this->_scontent .= "</".$name.">";
        } else if (($this->_ttag === true) and ($name !== 'target')) {
            $this->_tcontent .= "</".$name.">";
        } else {
            switch (strtolower($name)) {
                case 'trans-unit':
                    $this->_transunit = null;
                    $this->_langId    = null;
                    $this->_scontent  = null;
                    $this->_tcontent  = null;
                    break;
                case 'source':
                    if ($this->_useId) {
                        if (!empty($this->_scontent) && !empty($this->_langId) &&
                            !isset($this->_data[$this->_source][$this->_langId])) {
                            $this->_data[$this->_source][$this->_langId] = $this->_scontent;
                        }
                    } else {
                        if (!empty($this->_scontent) &&
                            !isset($this->_data[$this->_source][$this->_scontent])) {
                            $this->_data[$this->_source][$this->_scontent] = $this->_scontent;
                        }
                    }
                    $this->_stag = false;
                    break;
                case 'target':
                    if ($this->_useId) {
                        if (!empty($this->_tcontent) && !empty($this->_langId) &&
                            !isset($this->_data[$this->_target][$this->_langId])) {
                            $this->_data[$this->_target][$this->_langId] = $this->_tcontent;
                        }
                    } else {
                        if (!empty($this->_tcontent) && !empty($this->_scontent) &&
                            !isset($this->_data[$this->_target][$this->_scontent])) {
                            $this->_data[$this->_target][$this->_scontent] = $this->_tcontent;
                        }
                    }
                    $this->_ttag = false;
                    break;
                default:
                    break;
            }
        }
    }

    private function _contentElement($file, $data)
    {
        if (($this->_transunit !== null) and ($this->_source !== null) and ($this->_stag === true)) {
            $this->_scontent .= $data;
        }

        if (($this->_transunit !== null) and ($this->_target !== null) and ($this->_ttag === true)) {
            $this->_tcontent .= $data;
        }
    }

    private function _findEncoding($filename)
    {
        $file = file_get_contents($filename, null, null, 0, 100);
        if (strpos($file, "encoding") !== false) {
            $encoding = substr($file, strpos($file, "encoding") + 9);
            $encoding = substr($encoding, 1, strpos($encoding, $encoding[0], 1) - 1);
            return $encoding;
        }
        return 'UTF-8';
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return "Xliff";
    }
}
