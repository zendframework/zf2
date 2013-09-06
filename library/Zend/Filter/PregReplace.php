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
 * Filter that uses preg_replace method to perform a replacement in a string
 */
class PregReplace extends AbstractFilter
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $replacement = '';

    /**
     * Set the regex pattern to search for
     *
     * @see preg_replace()
     *
     * @param  string|array $pattern - same as the first argument of preg_replace
     * @return PregReplace
     * @throws Exception\InvalidArgumentException
     */
    public function setPattern($pattern)
    {
        if (!is_array($pattern) && !is_string($pattern)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects pattern to be array or string; received "%s"',
                __METHOD__,
                (is_object($pattern) ? get_class($pattern) : gettype($pattern))
            ));
        }

        $pattern = (array) $pattern;

        foreach ($pattern as $p) {
            $this->validatePattern($p);
        }

        $this->pattern = $pattern;
    }

    /**
     * Get currently set match pattern
     *
     * @return string|array
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set the replacement array/string
     *
     * @see preg_replace()
     * @param  array|string $replacement - same as the second argument of preg_replace
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setReplacement($replacement)
    {
        if (!is_array($replacement) && !is_string($replacement)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects replacement to be array or string; received "%s"',
                __METHOD__,
                (is_object($replacement) ? get_class($replacement) : gettype($replacement))
            ));
        }

        $this->replacement = $replacement;
    }

    /**
     * Get currently set replacement value
     *
     * @return string|array
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * Perform regexp replacement as filter
     * {@inheritDoc}
     */
    public function filter($value)
    {
        if (null === $this->pattern) {
            throw new Exception\RuntimeException(sprintf(
                'Filter "%s" does not have a valid pattern set',
                get_class($this)
            ));
        }

        return preg_replace($this->pattern, $this->replacement, $value);
    }

    /**
     * Validate a pattern and ensure it does not contain the "e" modifier
     *
     * @param  string $pattern
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    protected function validatePattern($pattern)
    {
        if (!preg_match('/(?<modifier>[imsxeADSUXJu]+)$/', $pattern, $matches)) {
            return true;
        }

        if (false !== strstr($matches['modifier'], 'e')) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Pattern for a PregReplace filter may not contain the "e" pattern modifier; received "%s"',
                $pattern
            ));
        }
    }
}
