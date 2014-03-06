<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 16.Oct.2013
 */
return array(
    // Set plural form
    '' => array('plural_forms' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);'),

    // Zend_Captcha_ReCaptcha
    "Missing captcha fields" => "Brakujące pole captcha",
    "Failed to validate captcha" => "Błąd podczas sprawdzania captcha",
    "Captcha value is wrong: %value%" => "Wartość captcha jest niepoprawna: %value%",

    // Zend_Captcha_Word
    "Empty captcha value" => "Pusta wartość captcha",
    "Captcha ID field is missing" => "Pole captcha ID jest puste",
    "Captcha value is wrong" => "Wartość captcha jest niepoprawna",
);
