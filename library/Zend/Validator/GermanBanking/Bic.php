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
 * Validator for German BIC (Bank Identifier Code).
 *
 * This constraint depends on malkusch/bav.
 *
 * Validation of a German BIC uses the library BAV. BAV's default configuration
 * is not recommended for BIC validation. Use a configuration with one of the
 * following DataBackendContainer implementations: PDODataBackendContainer or
 * DoctrineBackendContainer.
 *
 * @see \malkusch\bav\BAV::isValidBIC()
 * @see \malkusch\bav\ConfigurationRegistry::setConfiguration()
 */
class Bic extends AbstractValidator
{
    /**
     * @const string Error constants
     */
    const NO_BIC   = 'noBic';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::NO_BIC => "The input is not a German BIC",
    );

    /**
     * @var BAV
     */
    private $bav;

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->bav = new BAV();
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if (!$this->bav->isValidBIC($value)) {
            $this->error(self::NO_BIC);
            return false;
        }

        return true;
    }
}
