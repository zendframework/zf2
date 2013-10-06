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

/**
 * Validate credit card information
 *
 * Accepted options are:
 *      - service
 *      - allowed_types
 */
class CreditCard extends AbstractValidator
{
    /**
     * Error codes
     */
    const CHECKSUM        = 'creditcardChecksum';
    const CONTENT         = 'creditcardContent';
    const INVALID         = 'creditcardInvalid';
    const LENGTH          = 'creditcardLength';
    const PREFIX          = 'creditcardPrefix';
    const SERVICE         = 'creditcardService';

    /**
     * Detected CCI list
     *
     * @var string
     */
    const ALL              = 'All';
    const AMERICAN_EXPRESS = 'American_Express';
    const UNIONPAY         = 'Unionpay';
    const DINERS_CLUB      = 'Diners_Club';
    const DINERS_CLUB_US   = 'Diners_Club_US';
    const DISCOVER         = 'Discover';
    const JCB              = 'JCB';
    const LASER            = 'Laser';
    const MAESTRO          = 'Maestro';
    const MASTERCARD       = 'Mastercard';
    const SOLO             = 'Solo';
    const VISA             = 'Visa';

    /**
     * Validation error message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::CHECKSUM       => "The input seems to contain an invalid checksum",
        self::CONTENT        => "The input must contain only digits",
        self::INVALID        => "Invalid type given. String expected",
        self::LENGTH         => "The input contains an invalid amount of digits",
        self::PREFIX         => "The input is not from an allowed institute",
        self::SERVICE        => "The input seems to be an invalid credit card number",
        self::SERVICEFAILURE => "An exception has been raised while validating the input",
    );

    /**
     * List of allowed CCV lengths
     *
     * @var array
     */
    protected $cardLength = array(
        self::AMERICAN_EXPRESS => array(15),
        self::DINERS_CLUB      => array(14),
        self::DINERS_CLUB_US   => array(16),
        self::DISCOVER         => array(16),
        self::JCB              => array(16),
        self::LASER            => array(16, 17, 18, 19),
        self::MAESTRO          => array(12, 13, 14, 15, 16, 17, 18, 19),
        self::MASTERCARD       => array(16),
        self::SOLO             => array(16, 18, 19),
        self::UNIONPAY         => array(16, 17, 18, 19),
        self::VISA             => array(16),
    );

    /**
     * List of accepted CCV provider tags
     *
     * @var array
     */
    protected $cardType = array(
        self::AMERICAN_EXPRESS => array('34', '37'),
        self::DINERS_CLUB      => array('300', '301', '302', '303', '304', '305', '36'),
        self::DINERS_CLUB_US   => array('54', '55'),
        self::DISCOVER         => array('6011', '622126', '622127', '622128', '622129', '62213',
                                        '62214', '62215', '62216', '62217', '62218', '62219',
                                        '6222', '6223', '6224', '6225', '6226', '6227', '6228',
                                        '62290', '62291', '622920', '622921', '622922', '622923',
                                        '622924', '622925', '644', '645', '646', '647', '648',
                                        '649', '65'),
        self::JCB              => array('3528', '3529', '353', '354', '355', '356', '357', '358'),
        self::LASER            => array('6304', '6706', '6771', '6709'),
        self::MAESTRO          => array('5018', '5020', '5038', '6304', '6759', '6761', '6762', '6763',
                                        '6764', '6765', '6766'),
        self::MASTERCARD       => array('51', '52', '53', '54', '55'),
        self::SOLO             => array('6334', '6767'),
        self::UNIONPAY         => array('622126', '622127', '622128', '622129', '62213', '62214',
                                        '62215', '62216', '62217', '62218', '62219', '6222', '6223',
                                        '6224', '6225', '6226', '6227', '6228', '62290', '62291',
                                        '622920', '622921', '622922', '622923', '622924', '622925'),
        self::VISA             => array('4'),
    );

    /**
     * Additional service callback for validation
     *
     * @var callable
     */
    protected $service;

    /**
     * CCIs which are accepted by validation
     *
     * @var array
     */
    protected $allowedTypes = array(self::ALL);

    /**
     * Sets CCIs which are accepted by validation
     *
     * @param  array $allowedTypes Types to allow for validation
     * @return void
     */
    public function setAllowedTypes(array $allowedTypes)
    {
        $this->allowedTypes = array();

        foreach ($allowedTypes as $allowedType) {
            $this->addAllowedType($allowedType);
        }
    }

    /**
     * Adds a CCI to be accepted by validation
     *
     * @param  string $allowedType Type to allow for validation
     * @return void
     */
    public function addAllowedType($allowedType)
    {
        if ($allowedType === self::ALL) {
            $this->allowedTypes = array_keys($this->cardType);
            return;
        }

        $this->allowedTypes[] = $allowedType;
    }

    /**
     * Returns a list of accepted CCIs
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        return $this->allowedTypes;
    }

    /**
     * Sets a new callback for service validation
     *
     * @param  callable $service
     * @return void
     */
    public function setService(callable $service)
    {
        $this->service = $service;
    }

    /**
     * Returns the actual set service
     *
     * @return callable
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns true if and only if $value follows the Luhn algorithm (mod-10 checksum)
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if (!ctype_digit($data)) {
            return $this->buildErrorValidationResult($data, self::CONTENT);
        }

        $length = strlen($data);
        $types  = $this->getAllowedTypes();
        $foundp = false;
        $foundl = false;

        foreach ($types as $type) {
            foreach ($this->cardType[$type] as $prefix) {
                if (substr($data, 0, strlen($prefix)) == $prefix) {
                    $foundp = true;
                    if (in_array($length, $this->cardLength[$type])) {
                        $foundl = true;
                        break 2;
                    }
                }
            }
        }

        if ($foundp == false) {
            return $this->buildErrorValidationResult($data, self::PREFIX);
        }

        if ($foundl == false) {
            return $this->buildErrorValidationResult($data, self::LENGTH);
        }

        $sum    = 0;
        $weight = 2;

        for ($i = $length - 2; $i >= 0; $i--) {
            $digit = $weight * $data[$i];
            $sum += floor($digit / 10) + $digit % 10;
            $weight = $weight % 2 + 1;
        }

        if ((10 - $sum % 10) % 10 != $data[$length - 1]) {
            return $this->buildErrorValidationResult($data, self::CHECKSUM);
        }

        $service = $this->getService();

        if ($service && !$service($data, $context)) {
            return $this->buildErrorValidationResult($data, self::SERVICE);
        }

        return new ValidationResult($data);
    }
}
