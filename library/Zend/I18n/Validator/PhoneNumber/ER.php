<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'code' => '291',
    'patterns' => [
        'national' => [
            'general' => '/^[178]\\d{6}$/',
            'fixed' => '/^1(?:1[12568]|20|40|55|6[146])\\d{4}|8\\d{6}$/',
            'mobile' => '/^17[1-3]\\d{4}|7\\d{6}$/',
        ],
        'possible' => [
            'general' => '/^\\d{6,7}$/',
            'mobile' => '/^\\d{7}$/',
        ],
    ],
];
