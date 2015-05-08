<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'code' => '690',
    'patterns' => [
        'national' => [
            'general' => '/^[2-5]\\d{3}$/',
            'fixed' => '/^[2-4]\\d{3}$/',
            'mobile' => '/^5\\d{3}$/',
        ],
        'possible' => [
            'general' => '/^\\d{4}$/',
        ],
    ],
];
