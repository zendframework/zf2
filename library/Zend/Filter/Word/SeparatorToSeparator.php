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
 * Filter that transforms any separator words to other separator (eg.: my/example to my-example)
 */
class SeparatorToSeparator extends AbstractFilter
{
    /**
     * @var string
     */
    protected $searchSeparator = ' ';

    /**
     * @var string
     */
    protected $replacementSeparator = '-';

    /**
     * Set a new separator to search for
     *
     * @param  string $separator
     * @return void
     */
    public function setSearchSeparator($separator)
    {
        $this->searchSeparator = (string) $separator;
    }

    /**
     * Returns the actual set separator to search for
     *
     * @return  string
     */
    public function getSearchSeparator()
    {
        return $this->searchSeparator;
    }

    /**
     * Sets a new separator which replaces the searched one
     *
     * @param  string  $separator  Separator which replaces the searched one
     * @return void
     */
    public function setReplacementSeparator($separator)
    {
        $this->replacementSeparator = (string) $separator;
    }

    /**
     * Returns the actual set separator which replaces the searched one
     *
     * @return  string
     */
    public function getReplacementSeparator()
    {
        return $this->replacementSeparator;
    }

    /**
     * Returns the string $value, replacing the searched separators with the defined ones
     *
     * {@inheritDoc}
     */
    public function filter($value)
    {
        // @TODO: do we need to use a regex here? Can't we use str_replace?

        $pattern = '#' . preg_quote($this->searchSeparator, '#') . '#';
        return preg_replace($pattern, $this->replacementSeparator, $value);
    }
}
