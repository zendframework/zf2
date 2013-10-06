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
use Zend\Validator\Result\ValidationResult;

class Identical extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_SAME      = 'notSame';
    const MISSING_TOKEN = 'missingToken';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SAME      => "The two given tokens do not match",
        self::MISSING_TOKEN => 'No token was provided to match against',
    );

    /**
     * Original token against which to validate
     *
     * @var string
     */
    protected $tokenString;

    /**
     * Token with which the input will be validated against
     *
     * @var
     */
    protected $token;

    /**
     * Define if the validation should be done strict
     *
     * @var bool
     */
    protected $strict  = true;

    /**
     * If set to TRUE, the validation will skip the lookup for elements in the form context, and
     * validate the token just the way it was provided
     *
     * @var bool
     */
    protected $literal = false;

    /**
     * Set token against which to compare
     *
     * @param  mixed $token
     * @return void
     */
    public function setToken($token)
    {
        $this->tokenString = (is_array($token) ? var_export($token, true) : (string) $token);
        $this->token       = $token;
    }

    /**
     * Retrieve token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the strict parameter
     *
     * @param  bool $strict
     * @return void
     */
    public function setStrict($strict)
    {
        $this->strict = (bool) $strict;
    }

    /**
     * Returns the strict parameter
     *
     * @return bool
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * Sets the literal parameter
     *
     * @param  bool $literal
     * @return void
     */
    public function setLiteral($literal)
    {
        $this->literal = (bool) $literal;
    }

    /**
     * Returns the literal parameter
     *
     * @return bool
     */
    public function getLiteral()
    {
        return $this->literal;
    }

    /**
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * {@inheritDoc}
     * @throws Exception\RuntimeException if the token doesn't exist in the context array
     */
    public function validate($data, $context = null)
    {
        $token = $this->getToken();

        if (!$this->getLiteral() && $context !== null) {
            if (is_array($token)) {
                while (is_array($token)) {
                    $key = key($token);
                    if (!isset($context[$key])) {
                        break;
                    }
                    $context = $context[$key];
                    $token   = $token[$key];
                }
            }

            // if $token is an array it means the above loop didn't went all the way down to the leaf,
            // so the $token structure doesn't match the $context structure
            if (is_array($token) || !isset($context[$token])) {
                $token = $this->getToken();
            } else {
                $token = $context[$token];
            }
        }

        if (null === $token) {
            return $this->buildErrorValidationResult($data, self::MISSING_TOKEN);
        }

        $strict = $this->getStrict();

        if (($strict && ($data !== $token)) || (!$strict && ($data != $token))) {
            return $this->buildErrorValidationResult($data, self::NOT_SAME);
        }

        return new ValidationResult($data);
    }
}
