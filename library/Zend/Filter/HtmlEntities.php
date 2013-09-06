<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

/**
 * Filter that convert characters to their HTML representation
 *
 * NOTE: if you are using this to sanitize your values, you'd better use the Escaper
 * component in your views, as it is more secure
 */
class HtmlEntities extends AbstractFilter
{
    /**
     * The quote style
     *
     * @var int
     */
    protected $quoteStyle = ENT_QUOTES;

    /**
     * The encoding
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * If set to false, PHP will not encode existing HTML entities
     *
     * @var bool
     */
    protected $doubleEncode = true;

    /**
     * Set the quote style option
     *
     * @param  int $quoteStyle
     * @return void
     */
    public function setQuoteStyle($quoteStyle)
    {
        $this->quoteStyle = (int) $quoteStyle;
    }

    /**
     * Get the quote style option
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
     * Set the double encode option
     *
     * @param  bool $doubleEncode
     * @return void
     */
    public function setDoubleEncode($doubleEncode)
    {
        $this->doubleEncode = (bool) $doubleEncode;
    }

    /**
     * Returns the double quote option
     *
     * @return bool
     */
    public function getDoubleEncode()
    {
        return $this->doubleEncode;
    }

    /**
     * Returns the string $value, converting characters to their corresponding HTML entity
     * equivalents where they exist
     *
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $filtered = htmlentities((string) $value, $this->quoteStyle, $this->encoding, $this->doubleEncode);

        if (strlen((string) $value) && !strlen($filtered)) {
            if (!function_exists('iconv')) {
                throw new Exception\DomainException('Encoding mismatch has resulted in htmlentities errors');
            }

            // Ignored characters that cannot be represented in the target encoding
            $value    = iconv('', $this->encoding . '//IGNORE', (string) $value);
            $filtered = htmlentities($value, $this->quoteStyle, $this->encoding, $this->doubleEncode);

            if (!strlen($filtered)) {
                throw new Exception\DomainException('Encoding mismatch has resulted in htmlentities errors');
            }
        }

        return $filtered;
    }
}
