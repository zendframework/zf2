<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\ErrorHandler;
use Zend\Validator\Result\ValidationResult;

/**
 * Validate a string against a pattern
 *
 * Accepted options are:
 *      - pattern
 */
class Regex extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID   = 'regexInvalid';
    const NOT_MATCH = 'regexNotMatch';
    const ERROROUS  = 'regexErrorous';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => "The input does not match against pattern '%pattern%'",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    );

    /**
     * @var array
     */
    protected $messageVariables = array('pattern');

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * Sets the pattern option
     *
     * @param  string $pattern
     * @throws Exception\InvalidArgumentException if there is a fatal error in pattern matching
     * @return void
     */
    public function setPattern($pattern)
    {
        ErrorHandler::start();
        $this->pattern = (string) $pattern;
        $status        = preg_match($this->pattern, "Test");
        $error         = ErrorHandler::stop();

        if (false === $status) {
            throw new Exception\InvalidArgumentException("Internal error parsing the pattern '{$this->pattern}'", 0, $error);
        }
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Returns true if and only if $value matches against the pattern option
     *
     * {@inheritDoc}
     * @throws Exception\InvalidArgumentException
     */
    public function validate($data, $context = null)
    {
        if (null === $this->pattern) {
            throw new Exception\InvalidArgumentException("Missing option 'pattern'");
        }

        if (!is_string($data) && !is_int($data) && !is_float($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        ErrorHandler::start();
        $status = preg_match($this->pattern, $data);
        ErrorHandler::stop();

        if (false === $status) {
            return $this->buildErrorValidationResult($data, self::ERROROUS);
        }

        if (!$status) {
            return $this->buildErrorValidationResult($data, self::NOT_MATCH);
        }

        return new ValidationResult($data);
    }
}
