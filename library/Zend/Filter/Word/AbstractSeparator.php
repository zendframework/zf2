<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Word;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Base class for all word filters
 */
abstract class AbstractSeparator extends AbstractFilter
{
    /**
     * @var string
     */
    protected $separator = ' ';

    /**
     * Set a new separator
     *
     * @param  string $separator
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setSeparator($separator)
    {
        if (!is_string($separator)) {
            throw new Exception\InvalidArgumentException('"' . $separator . '" is not a valid separator.');
        }

        $this->separator = $separator;
    }

    /**
     * Get the actual set separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }
}
