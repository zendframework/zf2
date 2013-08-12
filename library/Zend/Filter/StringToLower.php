<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class StringToLower extends AbstractUnicode
{
    /**
     * Returns the string $value, converting characters to lowercase as necessary
     * {@inheritDoc}
     */
    public function filter($value)
    {
        if (null !== $this->getEncoding()) {
            return mb_strtolower((string) $value,  $this->encoding);
        }

        return strtolower((string) $value);
    }
}
