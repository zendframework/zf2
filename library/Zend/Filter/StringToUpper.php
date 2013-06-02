<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class StringToUpper extends AbstractUnicode
{
    /**
     * Returns the string $value, converting characters to lowercase as necessary
     * {@inheritDoc}
     */
    public function filter($value)
    {
        if (null !== $this->encoding) {
            return mb_strtoupper((string) $value,  $this->encoding);
        }

        return strtoupper((string) $value);
    }
}
