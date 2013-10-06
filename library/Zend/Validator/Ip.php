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
use Zend\Validator\Result\ValidationResult;

class Ip extends AbstractValidator
{
    /**
     * Error codes
     */
    const INVALID        = 'ipInvalid';
    const NOT_IP_ADDRESS = 'notIpAddress';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID        => 'Invalid type given. String expected',
        self::NOT_IP_ADDRESS => "The input does not appear to be a valid IP address",
    );

    /**
     * Enable IPv4 validation
     *
     * @var bool
     */
    protected $allowIPv4 = true;

    /**
     * Enable IPv6 validation
     *
     * @var bool
     */
    protected $allowIPv6 = true;

    /**
     * Enable IPvFuture validation
     *
     * @var bool
     */
    protected $allowIPvFuture = false;

    /**
     * Enable IPs in literal format (only IPv6 and IPvFuture)
     *
     * @var bool
     */
    protected $allowLiteral = true;

    /**
     * @param  boolean $allowIPv4
     * @return void
     */
    public function setAllowIPv4($allowIPv4)
    {
        $this->allowIPv4 = (bool) $allowIPv4;
    }

    /**
     * @return boolean
     */
    public function getAllowIPv4()
    {
        return $this->allowIPv4;
    }

    /**
     * @param boolean $allowIPv6
     * @return void
     */
    public function setAllowIPv6($allowIPv6)
    {
        $this->allowIPv6 = (bool) $allowIPv6;
    }

    /**
     * @return boolean
     */
    public function getAllowIPv6()
    {
        return $this->allowIPv6;
    }

    /**
     * @param boolean $allowIPvFuture
     * @return void
     */
    public function setAllowIPvFuture($allowIPvFuture)
    {
        $this->allowIPvFuture = (bool) $allowIPvFuture;
    }

    /**
     * @return boolean
     */
    public function getAllowIPvFuture()
    {
        return $this->allowIPvFuture;
    }

    /**
     * @param boolean $allowLiteral
     * @return void
     */
    public function setAllowLiteral($allowLiteral)
    {
        $this->allowLiteral = (bool) $allowLiteral;
    }

    /**
     * @return boolean
     */
    public function getAllowLiteral()
    {
        return $this->allowLiteral;
    }


    /**
     * Returns true if and only if $value is a valid IP address
     *
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        if (!is_string($data)) {
            return $this->buildErrorValidationResult($data, self::INVALID);
        }

        if ($this->allowIPv4 && $this->validateIPv4($data)) {
            return new ValidationResult($data);
        } else {
            if ($this->allowLiteral) {
                static $regex = '/^\[(.*)\]$/';

                if ((bool) preg_match($regex, $data, $matches)) {
                    $value = $matches[1];
                }
            }

            if ($this->allowIPv6 && $this->validateIPv6($data)
                || $this->allowIPvFuture && $this->validateIPvFuture($data)
            ) {
                return new ValidationResult($data);
            }
        }

        return $this->buildErrorValidationResult($data, self::NOT_IP_ADDRESS);
    }

    /**
     * Validates an IPv4 address
     *
     * @param string $value
     * @return bool
     */
    protected function validateIPv4($value)
    {
        if (preg_match('/^([01]{8}.){3}[01]{8}$/i', $value)) {
            // binary format  00000000.00000000.00000000.00000000
            $value = bindec(substr($value, 0, 8)) . '.' . bindec(substr($value, 9, 8)) . '.'
                   . bindec(substr($value, 18, 8)) . '.' . bindec(substr($value, 27, 8));
        } elseif (preg_match('/^([0-9]{3}.){3}[0-9]{3}$/i', $value)) {
            // octet format 777.777.777.777
            $value = (int) substr($value, 0, 3) . '.' . (int) substr($value, 4, 3) . '.'
                   . (int) substr($value, 8, 3) . '.' . (int) substr($value, 12, 3);
        } elseif (preg_match('/^([0-9a-f]{2}.){3}[0-9a-f]{2}$/i', $value)) {
            // hex format ff.ff.ff.ff
            $value = hexdec(substr($value, 0, 2)) . '.' . hexdec(substr($value, 3, 2)) . '.'
                   . hexdec(substr($value, 6, 2)) . '.' . hexdec(substr($value, 9, 2));
        }

        $ip2long = ip2long($value);

        if ($ip2long === false) {
            return false;
        }

        return ($value == long2ip($ip2long));
    }

    /**
     * Validates an IPv6 address
     *
     * @param  string $value Value to check against
     * @return bool True when $value is a valid ipv6 address
     *                 False otherwise
     */
    protected function validateIPv6($value)
    {
        if (strlen($value) < 3) {
            return $value == '::';
        }

        if (strpos($value, '.')) {
            $lastcolon = strrpos($value, ':');
            if (!($lastcolon && $this->validateIPv4(substr($value, $lastcolon + 1)))) {
                return false;
            }

            $value = substr($value, 0, $lastcolon) . ':0:0';
        }

        if (strpos($value, '::') === false) {
            return preg_match('/\A(?:[a-f0-9]{1,4}:){7}[a-f0-9]{1,4}\z/i', $value);
        }

        $colonCount = substr_count($value, ':');
        if ($colonCount < 8) {
            return preg_match('/\A(?::|(?:[a-f0-9]{1,4}:)+):(?:(?:[a-f0-9]{1,4}:)*[a-f0-9]{1,4})?\z/i', $value);
        }

        // special case with ending or starting double colon
        if ($colonCount == 8) {
            return preg_match('/\A(?:::)?(?:[a-f0-9]{1,4}:){6}[a-f0-9]{1,4}(?:::)?\z/i', $value);
        }

        return false;
    }

    /**
     * Validates an IPvFuture address.
     *
     * IPvFuture is loosely defined in the Section 3.2.2 of RFC 3986
     *
     * @param  string $value Value to check against
     * @return bool True when $value is a valid IPvFuture address
     *                 False otherwise
     */
    protected function validateIPvFuture($value)
    {
        /*
         * ABNF:
         * IPvFuture  = "v" 1*HEXDIG "." 1*( unreserved / sub-delims / ":" )
         * unreserved    = ALPHA / DIGIT / "-" / "." / "_" / "~"
         * sub-delims    = "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / ","
         *               / ";" / "="
         */
        static $regex = '/^v([[:xdigit:]]+)\.[[:alnum:]\-\._~!\$&\'\(\)\*\+,;=:]+$/';

        $result = (bool) preg_match($regex, $value, $matches);

        /*
         * "As such, implementations must not provide the version flag for the
         *  existing IPv4 and IPv6 literal address forms described below."
         */
        return ($result && $matches[1] != 4 && $matches[1] != 6);
    }
}
