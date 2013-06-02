<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;

class HtmlEntities extends AbstractFilter
{
    /**
     * Corresponds to the second htmlentities() argument
     *
     * @var int
     */
    protected $quoteStyle = ENT_QUOTES;

    /**
     * Corresponds to the third htmlentities() argument
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * Corresponds to the forth htmlentities() argument
     *
     * @var bool
     */
    protected $doubleQuote = true;

    /**
     * Sets the quote style option
     *
     * @param  int $quoteStyle
     * @return void
     */
    public function setQuoteStyle($quoteStyle)
    {
        $this->quoteStyle = (int) $quoteStyle;
    }

    /**
     * Returns the quote style option
     *
     * @return int
     */
    public function getQuoteStyle()
    {
        return $this->quoteStyle;
    }

    /**
     * Set encoding
     *
     * @param  string $value
     * @return void
     */
    public function setEncoding($value)
    {
        $this->encoding = (string) $value;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
         return $this->encoding;
    }

    /**
     * Sets the double quote option
     *
     * @param  bool $doubleQuote
     * @return void
     */
    public function setDoubleQuote($doubleQuote)
    {
        $this->doubleQuote = (bool) $doubleQuote;
    }

    /**
     * Returns the double quote option
     *
     * @return bool
     */
    public function getDoubleQuote()
    {
        return $this->doubleQuote;
    }

    /**
     * Returns the string $value, converting characters to their corresponding HTML entity
     * equivalents where they exist
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $filtered = htmlentities((string) $value, $this->quoteStyle, $this->encoding, $this->doubleQuote);

        if (strlen((string) $value) && !strlen($filtered)) {
            if (!function_exists('iconv')) {
                throw new Exception\DomainException('Encoding mismatch has resulted in htmlentities errors');
            }

            $enc      = $this->getEncoding();
            $value    = iconv('', $this->encoding . '//IGNORE', (string) $value);
            $filtered = htmlentities($value, $this->quoteStyle, $enc, $this->doubleQuote);

            if (!strlen($filtered)) {
                throw new Exception\DomainException('Encoding mismatch has resulted in htmlentities errors');
            }
        }

        return $filtered;
    }
}
