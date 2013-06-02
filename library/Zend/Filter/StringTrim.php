<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class StringTrim extends AbstractFilter
{
    /**
     * @var string
     */
    protected $charlist;

    /**
     * Sets the charlist option
     *
     * @param  string $charlist
     * @return void
     */
    public function setCharlist($charlist)
    {
        if (!empty($charList)) {
            $this->charlist = $charlist;
        }
    }

    /**
     * Returns the charlist option
     *
     * @return string|null
     */
    public function getCharlist()
    {
        return $this->charlist;
    }

    /**
     * Returns the string $value with characters stripped from the beginning and end
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // Do not filter non-string values
        if (!is_string($value)) {
            return $value;
        }

        if (null === $this->charlist) {
            return $this->unicodeTrim((string) $value);
        }

        return $this->unicodeTrim((string) $value, $this->charlist);
    }

    /**
     * Unicode aware trim method
     *
     * @param  string $value
     * @param  string $charlist
     * @return string
     */
    private function unicodeTrim($value, $charlist = '\\\\s')
    {
        $chars = preg_replace(
            array('/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'),
            array('\\\\\\0', '\\', '\/'),
            $charlist
        );

        $pattern = '/^[' . $chars . ']+|[' . $chars . ']+$/usSD';

        return preg_replace($pattern, '', $value);
    }
}
