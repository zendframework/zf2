<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'code' => '590',
    'patterns' => [
        'national' => [
            'general' => '/^[56]\\d{8}$/',
            'fixed' => '/^590(?:2[7-9]|5[12]|87)\\d{4}$/',
            'mobile' => '/^690(?:10|2[27]|66|77|8[78])\\d{4}$/',
            'emergency' => '/^18$/',
        ],
        'possible' => [
            'general' => '/^\\d{9}$/',
            'emergency' => '/^\\d{2}$/',
        ],
    ],
];
