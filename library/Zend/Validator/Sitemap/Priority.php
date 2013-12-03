<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator\Sitemap;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Result\ValidationResult;

/**
 * Validates whether a given value is valid as a sitemap <priority> value
 *
 * @link       http://www.sitemaps.org/protocol.php Sitemaps XML format
 */
class Priority extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_VALID = 'sitemapPriorityNotValid';
    const INVALID   = 'sitemapPriorityInvalid';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => "The input is not a valid sitemap priority",
        self::INVALID   => "Invalid type given. Numeric string, integer or float expected",
    );

    /**
     * Validates if a string is valid as a sitemap priority
     *
     * @link http://www.sitemaps.org/protocol.php#prioritydef <priority>
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_numeric($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        $data = (float) $data;
        if ($data < 0 || $data > 1) {
            return $this->buildErrorValidationResult($data, self::NOT_VALID);
        }

        return new ValidationResult($data);
    }
}
