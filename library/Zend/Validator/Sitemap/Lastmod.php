<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator\Sitemap;

use Zend\Stdlib\ErrorHandler;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Result\ValidationResult;

/**
 * Validates whether a given value is valid as a sitemap <lastmod> value
 *
 * @link       http://www.sitemaps.org/protocol.php Sitemaps XML format
 */
class Lastmod extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_VALID = 'sitemapLastmodNotValid';
    const INVALID   = 'sitemapLastmodInvalid';

    /**
     * Regular expression to use when validating
     */
    const LASTMOD_REGEX = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])(T([0-1][0-9]|2[0-3])(:[0-5][0-9])(:[0-5][0-9])?(\\+|-)([0-1][0-9]|2[0-3]):[0-5][0-9])?$/';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => "The input is not a valid sitemap lastmod",
        self::INVALID   => "Invalid type given. String expected",
    );

    /**
     * Validates if a string is valid as a sitemap lastmod
     *
     * @link http://www.sitemaps.org/protocol.php#lastmoddef <lastmod>
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        ErrorHandler::start();
        $result = preg_match(self::LASTMOD_REGEX, $data);
        ErrorHandler::stop();

        if ($result != 1) {
            return $this->buildErrorValidationResult($data, self::NOT_VALID);
        }

        return new ValidationResult($data);
    }
}
