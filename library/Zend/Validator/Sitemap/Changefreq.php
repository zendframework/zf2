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
 * Validates whether a given value is valid as a sitemap <changefreq> value
 *
 * @link       http://www.sitemaps.org/protocol.php Sitemaps XML format
 */
class Changefreq extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_VALID = 'sitemapChangefreqNotValid';
    const INVALID   = 'sitemapChangefreqInvalid';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => "The input is not a valid sitemap changefreq",
        self::INVALID   => "Invalid type given. String expected",
    );

    /**
     * Valid change frequencies
     *
     * @var array
     */
    protected $changeFreqs = array(
        'always',  'hourly', 'daily', 'weekly',
        'monthly', 'yearly', 'never'
    );

    /**
     * Validates if a string is valid as a sitemap changefreq
     *
     * @link http://www.sitemaps.org/protocol.php#changefreqdef <changefreq>
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if (!in_array($data, $this->changeFreqs, true)) {
            return $this->buildErrorValidationResult($data, self::NOT_VALID);
        }

        return new ValidationResult($data);
    }
}
