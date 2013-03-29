<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Validator;

use Traversable;
use Zend\I18n\CountryDb;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;

class CountryCode extends AbstractValidator
{

    const INVALID  = 'countryInvalid';
    const NO_MATCH = 'countryNoMatch';

    /**
     * @var array[string]
     */
    protected $messageTemplates = array(
        self::INVALID  => 'Invalid type given.  Scalar expected',
        self::NO_MATCH => 'The input does not appear to be a country',
    );

    /**
     * @var array[string]|null
     */
    protected $countries;

    /**
     * Constructor for the CountryCode validator
     *
     * Accepts an array countryTypes.
     * Or accepts an array countries.
     *
     * @param  array|Traversable $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (array_key_exists('loadCountryTypes', $options)) {
            $this->loadCountryTypes($options['loadCountryTypes']);
        } elseif (array_key_exists('countries', $options)) {
            $this->setCountries($options['countries']);
        }

        parent::__construct($options);
    }

    /**
     * Set Countries
     *
     * @param  array[string]   $countries
     * @return Country
     */
    public function setCountries(array $countries)
    {
        $this->countries = $countries;

        return $this;
    }

    /**
     * Load Country Types
     *
     * @param  array[string] $countryTypes
     * @return void
     */
    public function loadCountryTypes(array $countryTypes)
    {
        $countries = array();
        foreach ($countryTypes as $type) {
            $countries = array_merge($countries, CountryDb::getCountries($type));
        }
        $this->setCountries(array_keys($countries));
    }

    /**
     * Is Valid
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value = null)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);

            return false;
        }

        if (!isset($this->countries)) {
            $this->loadCountryTypes(array('official'));
        }

        $this->setValue($value);
        if (!in_array($value, $this->countries)) {
            $this->error(self::NO_MATCH);

            return false;
        }

        return true;
    }
}
