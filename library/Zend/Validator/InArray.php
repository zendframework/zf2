<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Zend\Validator\Result\ValidationResult;

/**
 * Validate that a given value is contains within an array
 *
 * Accepted options are:
 *      - haystack
 *      - strict
 *      - recursive
 */
class InArray extends AbstractValidator
{
    /**
     * Error code
     */
    const NOT_IN_ARRAY = 'notInArray';

    // Type of Strict check

    /**
     * Standard in_array strict checking value and type
     */
    const COMPARE_STRICT = 1;

    /**
     * Non strict check but prevents "asdf" == 0 returning TRUE causing false/positive.
     * This is the most secure option for non-strict checks and replaces strict = false
     * This will only be effective when the input is a string
     */
    const COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY = 0;

    /**
     * Standard non-strict check where "asdf" == 0 returns TRUE
     * This will be wanted when comparing "0" against int 0
     */
    const COMPARE_NOT_STRICT = -1;


    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_IN_ARRAY => 'The input was not found in the haystack',
    );

    /**
     * Haystack of possible values
     *
     * @var array
     */
    protected $haystack;

    /**
     * Type of strict check to be used. Due to "foo" == 0 === TRUE with in_array when strict = false,
     * an option has been added to prevent this. When $strict = 0/false, the most
     * secure non-strict check is implemented. if $strict = -1, the default in_array non-strict
     * behaviour is used
     *
     * @var int
     */
    protected $strict = self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY;

    /**
     * Whether a recursive search should be done
     *
     * @var bool
     */
    protected $recursive = false;

    /**
     * Sets the haystack option
     *
     * @param  array $haystack
     * @return void
     */
    public function setHaystack(array $haystack)
    {
        $this->haystack = $haystack;
    }

    /**
     * Returns the haystack option
     *
     * @return mixed
     * @throws Exception\RuntimeException if haystack option is not set
     */
    public function getHaystack()
    {
        return $this->haystack;
    }

    /**
     * Sets the strict option mode
     * InArray::CHECK_STRICT | InArray::CHECK_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY | InArray::CHECK_NOT_STRICT
     *
     * @param  int $strict
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setStrict($strict)
    {
        $checkTypes = array(
            self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY,    // 0
            self::COMPARE_STRICT,                                             // 1
            self::COMPARE_NOT_STRICT                                          // -1
        );

        // validate strict value
        if (!in_array($strict, $checkTypes)) {
            throw new Exception\InvalidArgumentException('Strict option must be one of the COMPARE_ constants');
        }

        $this->strict = $strict;
    }

    /**
     * Returns the strict option
     *
     * @return bool|int
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * Sets the recursive option
     *
     * @param  bool $recursive
     * @return void
     */
    public function setRecursive($recursive)
    {
        $this->recursive = (bool) $recursive;
    }

    /**
     * Returns the recursive option
     *
     * @return bool
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * Returns true if and only if $value is contained in the haystack option. If the strict
     * option is true, then the type of $value is also checked.
     *
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        // we create a copy of the haystack in case we need to modify it
        $haystack = $this->getHaystack();

        if ($haystack === null) {
            throw new Exception\RuntimeException('haystack option is mandatory');
        }

        // if the input is a string or float, and vulnerability protection is on
        // we type cast the input to a string
        if (self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY == $this->strict
            && (is_int($data) || is_float($data))) {
            $data = (string) $data;
        }

        if ($this->getRecursive()) {
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));

            foreach ($iterator as $element) {
                if ($this->strict === self::COMPARE_STRICT) {
                    if ($element === $data) {
                        return new ValidationResult($data);
                    }
                } else {
                    // add protection to prevent string to int vuln's
                    $el = $element;
                    if (self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY == $this->strict
                        && is_string($data) && (is_int($el) || is_float($el))
                    ) {
                        $el = (string) $el;
                    }

                    if ($el == $data) {
                        return new ValidationResult($data);
                    }
                }
            }
        } else {
            /**
             * If the check is not strict, then, to prevent "asdf" being converted to 0
             * and returning a false positive if 0 is in haystack, we type cast
             * the haystack to strings. To prevent "56asdf" == 56 === TRUE we also
             * type cast values like 56 to strings as well.
             *
             * This occurs only if the input is a string and a haystack member is an int
             */
            if (self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY == $this->strict
                && is_string($data)
            ) {
                foreach ($haystack as &$h) {
                    if (is_int($h) || is_float($h)) {
                        $h = (string) $h;
                    }
                }
            }

            if (in_array($data, $haystack, $this->strict == self::COMPARE_STRICT ? true : false)) {
                return new ValidationResult($data);
            }
        }

        return $this->buildErrorValidationResult($data, self::NOT_IN_ARRAY);
    }
}
