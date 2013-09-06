<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Filter;

use Locale;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Exception;

/**
 * Abstract class for all locale aware filters
 */
abstract class AbstractLocale extends AbstractFilter
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @param  array $options
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                '%s component requires the intl PHP extension',
                __NAMESPACE__
            ));
        }

        parent::__construct($options);
    }

    /**
     * Set the locale option
     *
     * @param  string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;
    }

    /**
     * Get the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        if (null == $this->locale) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }
}
