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
 * Validator for a German bank id (Bankleitzahl).
 * 
 * This constraint depends on malkusch/bav.
 */
class Blz extends AbstractValidator
{
    /**
     * @const string Error constants
     */
    const NO_BLZ   = 'noBlz';
    
    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::NO_BLZ => "The input is not a German bank id (Bankleitzahl)",
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
        
        if (!$this->bav->isValidBank($value)) {
            $this->error(self::NO_BLZ);
            return false;
        }
        
        return true;
    }
}
