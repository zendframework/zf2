<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Filter;

use NumberFormatter;
use Zend\I18n\Exception;
use Zend\Stdlib\ErrorHandler;

/**
 * Filter a number value according to a given locale
 */
class NumberFormat extends AbstractLocale
{
    /**
     * @var int
     */
    protected $style = NumberFormatter::DEFAULT_STYLE;

    /**
     * @var int
     */
    protected $type = NumberFormatter::TYPE_DOUBLE;

    /**
     * @var NumberFormatter
     */
    protected $formatter = null;

    /**
     * {@inheritDoc}
     */
    public function setLocale($locale)
    {
        parent::setLocale($locale);
        $this->formatter = null;
    }

    /**
     * @param  int $style
     * @return void
     */
    public function setStyle($style)
    {
        $this->style = (int) $style;
        $this->formatter = null;
    }

    /**
     * @return int
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param  int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = (int) $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  NumberFormatter $formatter
     * @return void
     */
    public function setFormatter(NumberFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return NumberFormatter
     * @throws Exception\RuntimeException
     */
    public function getFormatter()
    {
        if (null === $this->formatter) {
            $formatter = NumberFormatter::create($this->getLocale(), $this->getStyle());

            if (!$formatter) {
                throw new Exception\RuntimeException(
                    'Can not create NumberFormatter instance; ' . intl_get_error_message()
                );
            }

            $this->formatter = $formatter;
        }

        return $this->formatter;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($value)
    {
        $formatter = $this->getFormatter();
        $type      = $this->getType();

        if (is_int($value) || is_float($value)) {
            ErrorHandler::start();
            $result = $formatter->format($value, $type);
            ErrorHandler::stop();
        } else {
            $value = str_replace(array("\xC2\xA0", ' '), '', $value);
            ErrorHandler::start();
            $result = $formatter->parse($value, $type);
            ErrorHandler::stop();
        }

        if ($result === false) {
            return $value;
        }

        return str_replace("\xC2\xA0", ' ', $result);
    }
}
