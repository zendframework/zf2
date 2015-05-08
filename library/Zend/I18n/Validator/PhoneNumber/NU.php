<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'code' => '683',
    'patterns' => [
        'national' => [
            'general' => '/^[1-5]\\d{3}$/',
            'fixed' => '/^[34]\\d{3}$/',
            'mobile' => '/^[125]\\d{3}$/',
            'emergency' => '/^999$/',
        ],
        'possible' => [
            'general' => '/^\\d{4}$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
