<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator\GermanBanking;

use Zend\Validator\AbstractValidator;
use malkusch\bav\BAV;

/**
 * Validator for a German bank account.
 *
 * This constraint depends on malkusch/bav.
 */
class Konto extends AbstractValidator
{
    /**
     * @const string Error constants
     */
    const NO_ACCOUNT = 'noAccount';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::NO_ACCOUNT => "The input is not a German bank account",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        "bank" => null, // Bank id (Bankleitzahl) for validating the account.
    );

    /**
     * @var string Bank id (Bankleitzahl)
     */
    private $bank;

    /**
     * @var BAV
     */
    private $bav;

    /**
     * Sets validator options
     *
     * @param  string|array|Traversable $options OPTIONAL
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp["bank"] = array_shift($options);
            $options = $temp;
        }

        parent::__construct($options);

        $this->bav = new BAV();
    }

    /**
     * Sets the bank id (Bankleitzahl)
     *
     * An account can only be validated against a bank.
     *
     * @param string $bank Bank id (Bankleitzahl)
     */
    public function setBank($bank)
    {
        $this->options["bank"] = $bank;
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if (!$this->bav->isValidBankAccount($this->options["bank"], $value)) {
            $this->error(self::NO_ACCOUNT);
            return false;
        }

        return true;
    }
}
