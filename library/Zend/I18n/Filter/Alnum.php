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
use Zend\Stdlib\StringUtils;

/**
 * Filter a value so that non alphanumeric characters are removed
 */
class Alnum extends AbstractLocale
{
    /**
     * @var bool
     */
    protected $allowWhiteSpace = false;

    /**
     * Set if white space is allowed
     *
     * @param  bool $allowWhiteSpace
     * @return void
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->allowWhiteSpace = (bool) $allowWhiteSpace;
    }

    /**
     * Get whether white space is allowed
     *
     * @return bool
     */
    public function getAllowWhiteSpace()
    {
        return $this->allowWhiteSpace;
    }

    /**
     * Returns $value as string with all non-alphanumeric characters removed
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';
        $language   = Locale::getPrimaryLanguage($this->getLocale());

        if (!StringUtils::hasPcreUnicodeSupport()) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/';
        } elseif ($language == 'ja'|| $language == 'ko' || $language == 'zh') {
            // Use english alphabet
            $pattern = '/[^a-zA-Z0-9'  . $whiteSpace . ']/u';
        } else {
            // Use native language alphabet
            $pattern = '/[^\p{L}\p{N}' . $whiteSpace . ']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
