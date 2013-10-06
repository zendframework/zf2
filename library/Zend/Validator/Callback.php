<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Zend\Validator\Result\ValidationResult;

/**
 * A validator that uses a Callable to validate the data
 *
 * Accepted options are:
 *      - message_templates
 *      - message_variables
 *      - callback
 *      - callback_params
 */
class Callback extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID_CALLBACK = 'callbackInvalid';
    const INVALID_VALUE = 'callbackValue';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID_VALUE    => "The input is not valid",
        self::INVALID_CALLBACK => "An exception has been raised within the callback",
    );

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $callbackParams = array();

    /**
     * Sets a new callback for this filter
     *
     * @param  callable $callback
     * @return void
     */
    public function setCallback(callable $callback)
    {
        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        }

        $this->callback = $callback;
    }

    /**
     * Returns the set callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets parameters for the callback
     *
     * @param  array $params
     * @return Callback
     */
    public function setCallbackParams(array $params)
    {
        $this->callbackParams = $params;
    }

    /**
     * Get parameters for the callback
     *
     * @return mixed
     */
    public function getCallbackParams()
    {
        return $this->callbackParams;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (empty($this->callback)) {
            throw new Exception\InvalidArgumentException('No callback given');
        }

        $params = $this->callbackParams;
        $args   = array($data);

        if (empty($params) && !empty($context)) {
            $args[] = $context;
        }

        if (!empty($params) && empty($context)) {
            $args = array_merge($args, $params);
        }

        if (!empty($params) && !empty($context)) {
            $args[] = $context;
            $args   = array_merge($args, $params);
        }

        try {
            if (!call_user_func_array($this->callback, $args)) {
                return $this->buildErrorValidationResult($data, self::INVALID_VALUE);
            }
        } catch (\Exception $e) {
            return $this->buildErrorValidationResult($data, self::INVALID_CALLBACK);
        }

        return new ValidationResult($data);
    }
}
