<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Zend\Stdlib\StringUtils;
use Zend\Stdlib\StringWrapper\StringWrapperInterface as StringWrapper;
use Zend\Validator\Result\ValidationResult;

/**
 * A validator that can validate if a string length is inside bounds
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 *      - min
 *      - max
 *      - encoding
 */
class StringLength extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID   = 'stringLengthInvalid';
    const TOO_SHORT = 'stringLengthTooShort';
    const TOO_LONG  = 'stringLengthTooLong';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String expected",
        self::TOO_SHORT => "The input is less than %min% characters long",
        self::TOO_LONG  => "The input is more than %max% characters long",
    );

    /**
     * Variables that can get injected
     *
     * @var array
     */
    protected $messageVariables = array('min', 'max');

    /**
     * Minimum length
     *
     * @var int
     */
    protected $min = 0;

    /**
     * Maximum length (null if no upper limit)
     *
     * @var int|null
     */
    protected $max = null;

    /**
     * Encoding to use
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * @var StringWrapper
     */
    protected $stringWrapper;

    /**
     * Set the min length
     *
     * @param  int $min
     * @return void
     */
    public function setMin($min)
    {
        $this->min = max(0, (int) $min);
    }

    /**
     * Returns the min length
     *
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set the max option
     *
     * @param  int|null $max
     * @return void
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * Returns the max option
     *
     * @return int|null
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Get the string wrapper to detect the string length
     *
     * @return StringWrapper
     */
    public function getStringWrapper()
    {
        if (!$this->stringWrapper) {
            $this->stringWrapper = StringUtils::getWrapper($this->getEncoding());
        }

        return $this->stringWrapper;
    }

    /**
     * Set the string wrapper to detect the string length
     *
     * @param StringWrapper $stringWrapper
     * @return StringLength
     */
    public function setStringWrapper(StringWrapper $stringWrapper)
    {
        $stringWrapper->setEncoding($this->getEncoding());
        $this->stringWrapper = $stringWrapper;
    }

    /**
     * Set a new encoding to use
     *
     * @param  string $encoding
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->stringWrapper = StringUtils::getWrapper($encoding);
        $this->encoding      = $encoding;
    }

    /**
     * Get the actual encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if ($this->max !== null && $this->min > $this->max) {
            throw new Exception\InvalidArgumentException('Min value cannot be higher than max value');
        }

        $length     = $this->getStringWrapper()->strlen($data);
        $errorCodes = array();

        if ($length < $this->min) {
            $errorCodes[] = self::TOO_SHORT;
        }

        if (null !== $this->getMax() && $this->getMax() < $length) {
            $errorCodes[] = self::TOO_LONG;
        }

        if (!empty($errorCodes)) {
            return $this->buildErrorValidationResult($data, $errorCodes);
        }

        return new ValidationResult($data);
    }
}
