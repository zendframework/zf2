<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Validator;

use Zend\Validator\AbstractValidator;

class Iban extends AbstractValidator
{

    const NO_MATCH       = 'ibanNoMatch';
    const INVALID_LENGTH = 'ibanInvalidLength';
    const INVALID        = 'ibanInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NO_MATCH       => 'The input does not match the IBAN format',
        self::INVALID_LENGTH => 'The input must be either 8 or 11 characters',
        self::INVALID        => 'Invalid type given.  String expected',
    );

    /**
     * IBAN formats by ISO-3611
     *
     * @var array
     */
    protected $iban = array(
        'AD' => array(
            'bban' => '/^([0-9]{4})([0-9]{4})([A-Za-z0-9]{12})$/',
            'iban' => '/^(AD)([0-9]{2})([0-9]{4})([0-9]{4})([A-Za-z0-9]{12})$/',
        ),
        'AE' => array(
            'bban' => '/^([0-9]{3})([0-9]{16})$/',
            'iban' => '/^(AE)([0-9]{2})([0-9]{3})([A-Za-z0-9]{16})$/',
        ),
        'AL' => array(
            'bban' => '/^([0-9]{8})([A-Za-z0-9]{16})$/',
            'iban' => '/^(AL)([0-9]{2})([0-9]{8})([A-Za-z0-9]{16})$/',
        ),
        'AT' => array(
            'bban' => '/^([0-9]{5})([0-9]{11})$/',
            'iban' => '/^(AT)([0-9]{2})([0-9]{5})([0-9]{11})$/',
        ),
        'AZ' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{20})$/',
            'iban' => '/^(AZ)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{20})$/',
        ),
        'BA' => array(
            'bban' => '/^([0-9]{3})([0-9]{3})([0-9]{8})([0-9]{2})$/',
            'iban' => '/^(BA)([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{8})([0-9]{2})$/',
        ),
        'BE' => array(
            'bban' => '/^([0-9]{3})([0-9]{7})([0-9]{2})$/',
            'iban' => '/^(BE)([0-9]{2})([0-9]{3})([0-9]{7})([0-9]{2})$/',
        ),
        'BG' => array(
            'bban' => '/^([A-Z]{4})([0-9]{4})([0-9]{2})([A-Za-z0-9]{8})$/',
            'iban' => '/^(BG)([0-9]{2})([A-Z]{4})([0-9]{4})([0-9]{2})([A-Za-z0-9]{8})$/',
        ),
        'BH' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{14})$/',
            'iban' => '/^(BH)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{14})$/',
        ),
        'BR' => array(
            'bban' => '/^([0-9]{8})([0-9]{5})([0-9]{10})([A-Z]{1})([A-Za-z0-9]{1})$/',
            'iban' => '/^BR([0-9]{2})([0-9]{8})([0-9]{5})([0-9]{10})([A-Z]{1})([A-Za-z0-9]{1})$/',
        ),
        'CH' => array(
            'bban' => '/^([0-9]{5})([A-Za-z0-9]{12})$/',
            'iban' => '/^(CH)([0-9]{2})([0-9]{5})([A-Za-z0-9]{12})$/',
        ),
        'CR' => array(
            'bban' => '/^([0-9]{3})([0-9]{14})$/',
            'iban' => '/^(CR)([0-9]{2})([0-9]{3})([0-9]{14})$/',
        ),
        'CY' => array(
            'bban' => '/^([0-9]{3})([0-9]{5})([A-Za-z0-9]{16})$/',
            'iban' => '/^(CY)([0-9]{2})([0-9]{3})([0-9]{5})([A-Za-z0-9]{16})$/',
        ),
        'CZ' => array(
            'bban' => '/^([0-9]{4})([0-9]{6})([0-9]{10})$/',
            'iban' => '/^(CZ)([0-9]{2})([0-9]{4})([0-9]{6})([0-9]{10})$/',
        ),
        'DE' => array(
            'bban' => '/^([0-9]{8})([0-9]{10})$/',
            'iban' => '/^(DE)([0-9]{2})([0-9]{8})([0-9]{10})$/',
        ),
        'DK' => array(
            'bban' => '/^([0-9]{4})([0-9]{9})([0-9]{1})$/',
            'iban' => '/^(DK|FO|GL)([0-9]{2})([0-9]{4})([0-9]{9})([0-9]{1})$/',
        ),
        'DO' => array(
            'bban' => '/^([A-Za-z0-9]{4})([0-9]{20})$/',
            'iban' => '/^(DO)([0-9]{2})([A-Za-z0-9]{4})([0-9]{20})$/',
        ),
        'EE' => array(
            'bban' => '/^([0-9]{2})([0-9]{2})([0-9]{11})([0-9]{1})$/',
            'iban' => '/^(EE)([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{11})([0-9]{1})$/',
        ),
        'ES' => array(
            'bban' => '/^([0-9]{4})([0-9]{4})([0-9]{1})([0-9]{1})([0-9]{10})$/',
            'iban' => '/^(ES)([0-9]{2})([0-9]{4})([0-9]{4})([0-9]{1})([0-9]{1})([0-9]{10})$/',
        ),
        'FI' => array(
            'bban' => '',
            'iban' => '/^(FI|AX)([0-9]{2})([0-9]{6})([0-9]{7})([0-9]{1})$/',
        ),
        'FR' => array(
            'bban' => '/^([0-9]{5})([0-9]{5})([A-Za-z0-9]{11})([0-9]{2})$/',
            'iban' => '/^(FR|GF|GP|MQ|RE|PF|TF|YT|NC|BL|MF|PM|WF|GP|RE|MQ|GF|PM|YT)([0-9]{2})([0-9]{5})([0-9]{5})([A-Za-z0-9]{11})([0-9]{2})$/',
        ),
        'GB' => array(
            'bban' => '/^([A-Z]{4})([0-9]{6})([0-9]{8})$/',
            'iban' => '/^(GB)([0-9]{2})([A-Z]{4})([0-9]{6})([0-9]{8})$/',
        ),
        'GE' => array(
            'bban' => '/^([A-Z]{2})([0-9]{16})$/',
            'iban' => '/^(GE)([0-9]{2})([A-Z]{2})([0-9]{16})$/',
        ),
        'GI' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{15})$/',
            'iban' => '/^(GI)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{15})$/',
        ),
        'GR' => array(
            'bban' => '/^([0-9]{3})([0-9]{4})([A-Za-z0-9]{16})$/',
            'iban' => '/^(GR)([0-9]{2})([0-9]{3})([0-9]{4})([A-Za-z0-9]{16})$/',
        ),
        'GT' => array(
            'bban' => '/^([A-Za-z0-9]{4})([A-Za-z0-9]{20})$/',
            'iban' => '/^(GT)([0-9]{2})([A-Za-z0-9]{4})([A-Za-z0-9]{20})$/',
        ),
        'HR' => array(
            'bban' => '/^([0-9]{7})([0-9]{10})$/',
            'iban' => '/^(HR)([0-9]{2})([0-9]{7})([0-9]{10})$/',
        ),
        'HU' => array(
            'bban' => '/^([0-9]{3})([0-9]{4})([0-9]{1})([0-9]{15})([0-9]{1}):$/',
            'iban' => '/^(HU)([0-9]{2})([0-9]{3})([0-9]{4})([0-9]{1})([0-9]{15})([0-9]{1})$/',
        ),
        'IE' => array(
            'bban' => '/^([A-Z]{4})([0-9]{6})([0-9]{8})$/',
            'iban' => '/^(IE)([0-9]{2})([A-Z]{4})([0-9]{6})([0-9]{8})$/',
        ),
        'IL' => array(
            'bban' => '/^([0-9]{3})([0-9]{3})([0-9]{13})$/',
            'iban' => '/^(IL)([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{13})$/',
        ),
        'IS' => array(
            'bban' => '/^([0-9]{4})([0-9]{2})([0-9]{6})([0-9]{10})$/',
            'iban' => '/^(IS)([0-9]{2})([0-9]{4})([0-9]{2})([0-9]{6})([0-9]{10})$/',
        ),
        'IT' => array(
            'bban' => '/^([A-Z]{1})([0-9]{5})([0-9]{5})([A-Za-z0-9]{12})$/',
            'iban' => '/^(IT)([0-9]{2})([A-Z]{1})([0-9]{5})([0-9]{5})([A-Za-z0-9]{12})$/',
        ),
        'KW' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{22})$/',
            'iban' => '/^(KW)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{22})$/',
        ),
        'KZ' => array(
            'bban' => '/^([0-9]{3})([A-Za-z0-9]{13})$/',
            'iban' => '/^(KZ)([0-9]{2})([0-9]{3})([A-Za-z0-9]{13})$/',
        ),
        'LB' => array(
            'bban' => '/^([0-9]{4})([A-Za-z0-9]{20})$/',
            'iban' => '/^(LB)([0-9]{2})([0-9]{4})([A-Za-z0-9]{20})$/',
        ),
        'LI' => array(
            'bban' => '/^([0-9]{5})([A-Za-z0-9]{12})$/',
            'iban' => '/^(LI)([0-9]{2})([0-9]{5})([A-Za-z0-9]{12})$/',
        ),
        'LT' => array(
            'bban' => '/^([0-9]{5})([0-9]{11})$/',
            'iban' => '/^(LT)([0-9]{2})([0-9]{5})([0-9]{11})$/',
        ),
        'LU' => array(
            'bban' => '/^([0-9]{3})([A-Za-z0-9]{13})$/',
            'iban' => '/^(LU)([0-9]{2})([0-9]{3})([A-Za-z0-9]{13})$/',
        ),
        'LV' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{13})$/',
            'iban' => '/^(LV)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{13})$/',
        ),
        'MC' => array(
            'bban' => '/^([0-9]{5})([0-9]{5})([A-Za-z0-9]{11})([0-9]{2})$/',
            'iban' => '/^(MC)([0-9]{2})([0-9]{5})([0-9]{5})([A-Za-z0-9]{11})([0-9]{2})$/',
        ),
        'MD' => array(
            'bban' => '/^([A-Za-z0-9]{2})([A-Za-z0-9]{18})$/',
            'iban' => '/^(MD)([0-9]{2})([A-Za-z0-9]{20})$/',
        ),
        'ME' => array(
            'bban' => '/^([0-9]{3})([0-9]{13})([0-9]{2})$/',
            'iban' => '/^(ME)([0-9]{2})([0-9]{3})([0-9]{13})([0-9]{2})$/',
        ),
        'MK' => array(
            'bban' => '/^([0-9]{3})([A-Za-z0-9]{10})([0-9]{2})$/',
            'iban' => '/^(MK)([0-9]{2})([0-9]{3})([A-Za-z0-9]{10})([0-9]{2})$/',
        ),
        'MR' => array(
            'bban' => '/^([0-9]{5})([0-9]{5})([0-9]{11})([0-9]{2})$/',
            'iban' => '/^(MR)13([0-9]{5})([0-9]{5})([0-9]{11})([0-9]{2})$/',
        ),
        'MT' => array(
            'bban' => '/^([A-Z]{4})([0-9]{5})([A-Za-z0-9]{18})$/',
            'iban' => '/^(MT)([0-9]{2})([A-Z]{4})([0-9]{5})([A-Za-z0-9]{18})$/',
        ),
        'MU' => array(
            'bban' => '/^([A-Z]{4})([0-9]{2})([0-9]{2})([0-9]{12})([0-9]{3})([A-Z]{3})$/',
            'iban' => '/^(MU)([0-9]{2})([A-Z]{4})([0-9]{2})([0-9]{2})([0-9]{12})([0-9]{3})([A-Z]{3})$/',
        ),
        'NL' => array(
            'bban' => '/^([A-Z]{4})([0-9]{10})$/',
            'iban' => '/^(NL)([0-9]{2})([A-Z]{4})([0-9]{10})$/',
        ),
        'NO' => array(
            'bban' => '/^([0-9]{4})([0-9]{6})([0-9]{1})$/',
            'iban' => '/^(NO)([0-9]{2})([0-9]{4})([0-9]{6})([0-9]{1})$/',
        ),
        'PK' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{16})$/',
            'iban' => '/^(PK)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{16})$/',
        ),
        'PL' => array(
            'bban' => '/^([0-9]{8})([0-9]{16})$/',
            'iban' => '/^(PL)([0-9]{2})([0-9]{8})([0-9]{1,16})$/',
        ),
        'PS' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{21})$/',
            'iban' => '/^(PS)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{21})$/',
        ),
        'PT' => array(
            'bban' => '/^([0-9]{4})([0-9]{4})([0-9]{11})([0-9]{2})$/',
            'iban' => '/^(PT)([0-9]{2})([0-9]{4})([0-9]{4})([0-9]{11})([0-9]{2})$/',
        ),
        'RO' => array(
            'bban' => '/^([A-Z]{4})([A-Za-z0-9]{16})$/',
            'iban' => '/^(RO)([0-9]{2})([A-Z]{4})([A-Za-z0-9]{16})$/',
        ),
        'RS' => array(
            'bban' => '/^([0-9]{3})([0-9]{13})([0-9]{2})$/',
            'iban' => '/^(RS)([0-9]{2})([0-9]{3})([0-9]{13})([0-9]{2})$/',
        ),
        'SA' => array(
            'bban' => '/^([0-9]{2})([A-Za-z0-9]{18})$/',
            'iban' => '/^(SA)([0-9]{2})([0-9]{2})([A-Za-z0-9]{18})$/',
        ),
        'SE' => array(
            'bban' => '/^([0-9]{3})([0-9]{16})([0-9]{1})$/',
            'iban' => '/^(SE)([0-9]{2})([0-9]{3})([0-9]{16})([0-9]{1})$/',
        ),
        'SI' => array(
            'bban' => '/^([0-9]{5})([0-9]{8})([0-9]{2})$/',
            'iban' => '/^(SI)([0-9]{2})([0-9]{5})([0-9]{8})([0-9]{2})$/',
        ),
        'SK' => array(
            'bban' => '/^([0-9]{4})([0-9]{6})([0-9]{10})$/',
            'iban' => '/^(SK)([0-9]{2})([0-9]{4})([0-9]{6})([0-9]{10})$/',
        ),
        'SM' => array(
            'bban' => '/^([A-Z]{1})([0-9]{5})([0-9]{5})([A-Za-z0-9]{12})$/',
            'iban' => '/^(SM)([0-9]{2})([A-Z]{1})([0-9]{5})([0-9]{5})([A-Za-z0-9]{12})$/',
        ),
        'TN' => array(
            'bban' => '/^([0-9]{2})([0-9]{3})([0-9]{13})([0-9]{2})$/',
            'iban' => '/^(TN)59([0-9]{2})([0-9]{3})([0-9]{13})([0-9]{2})$/',
        ),
        'TR' => array(
            'bban' => '/^([0-9]{5})([A-Za-z0-9]{1})([A-Za-z0-9]{16})$/',
            'iban' => '/^(TR)([0-9]{2})([0-9]{5})([A-Za-z0-9]{1})([A-Za-z0-9]{16})$/',
        ),
        'VG' => array(
            'bban' => '/^([A-Z]{4})([0-9]{16})$/',
            'iban' => '/^(VG)([0-9]{2})([A-Z]{4})([0-9]{16})$/',
        ),
    );

    /**
     * ISO 3611 Country Code
     *
     * @var string
     */
    protected $country;

    /**
     * Allow BBAN
     *
     * @var boolean
     */
    protected $allowBban = false;

    /**
     * Constrcutor for the Iban validator
     *
     * Options
     * - country | string | field or value
     * - allow_bban | boolean | allow bban acccount numbers
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (array_key_exists('country', $options)) {
            $this->setCountry($options['country']);
        }

        if (array_key_exists('allow_bban', $options)) {
            $this->allowBban($options['allow_bban']);
        }

        parent::__construct($options);
    }

    /**
     * Allow Bban
     *
     * @param  boolean|null $allow
     * @return boolean|Iban
     */
    public function allowBban($allow = null)
    {
        if (null !== $allow) {
            $this->allowBban = (bool) $allow;

            return $this;
        }

        return $this->allowBban;
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Country
     *
     * @param  string $country
     * @return Iban
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Returns true if and only if $value matches iban format
     *
     * @param  string $value
     * @param  array  $context
     * @return bool
     */
    public function isValid($value = null, $context = null)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);

            return false;
        }

        $country = $this->getCountry();
        if (!isset($this->iban[$country])) {
            if (isset($context[$country])) {
                $country = $context[$country];
            } else {
                $country = substr($value, 0, 2);
            }

            if (!isset($this->iban[$country])) {
                throw new \InvalidArgumentException('No valid country provided for validation');
            }
        }

        if ($this->allowBban && $this->iban[$country]['bban']) {
            if (preg_match($this->iban[$country]['bban'], $value)) {
                return true;
            }
        }

        if (!preg_match($this->iban[$country]['iban'], $value)) {
            $this->error(self::NO_MATCH);

            return false;
        }

        return true;
    }
}
