<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 30.Jul.2011
 */
return array(
    // Set plural form
    '' => array('plural_forms' => 'nplurals=4; plural=(n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3);', ),

    // Zend\Captcha\ReCaptcha
    "Missing captcha fields" => "Manjka varnostna koda",
    "Failed to validate captcha" => "Varnostne kode ni bilo mogoče preveriti",
    "Captcha value is wrong: %value%" => "Napačna varnostna koda: %value%",

    // Zend\Captcha\Word
    "Empty captcha value" => "Prazna varnostna koda",
    "Captcha ID field is missing" => "Polje ID varnostne kode manjka",
    "Captcha value is wrong" => "Varnostna koda je napačna",
);
