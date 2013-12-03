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
 * Validate that a class name is an instance of specific object
 *
 * Accepted options are:
 *      - class_name
 */
class IsInstanceOf extends AbstractValidator
{
    /**
     * Error code
     */
    const NOT_INSTANCE_OF = 'notInstanceOf';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_INSTANCE_OF => "The input is not an instance of '%className%'",
    );

    /**
     * Variables that can get injected
     *
     * @var array
     */
    protected $messageVariables = array('className');

    /**
     * Class name
     *
     * @var string
     */
    protected $className;

    /**
     * Set class name
     *
     * @param  string $className
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = (string) $className;
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Returns true if $value is instance of $this->className
     *
     * {@inheritDoc}
     * @throws Exception\InvalidArgumentException
     */
    public function validate($data, $context = null)
    {
        if (null === $this->className) {
            throw new Exception\InvalidArgumentException('Missing option "className"');
        }

        if ($data instanceof $this->className) {
            return new ValidationResult($data);
        }

        return $this->buildErrorValidationResult($data, self::NOT_INSTANCE_OF);
    }
}
