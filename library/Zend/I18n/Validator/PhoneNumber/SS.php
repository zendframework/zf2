<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'code' => '211',
    'patterns' => [
        'national' => [
            'general' => '/^[19]\\d{8}$/',
            'fixed' => '/^18\\d{7}$/',
            'mobile' => '/^(?:12|9[1257])\\d{7}$/',
        ],
        'possible' => [
            'general' => '/^\\d{9}$/',
        ],
    ],
];
