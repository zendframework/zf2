<?php
return array(
    'code' => '54',
    'patterns' => array(
        'national' => array(
            'general' => '/^[1-368]\d{9}|9\d{10}$/',
            'fixed' => '/^11\d{8}|(?:2(?:2(?:[013]\d|2[13-79]|4[1-6]|5[2457]|6[124-8]|7[1-4]|8[13-6]|9[1267])|3(?:1[467]|2[03-6]|3[13-8]|[49][2-6]|5[2-8]|[067]\d)|4(?:7[3-8]|9\d)|6(?:[01346]\d|2[24-6]|5[15-8])|80\d|9(?:[0124789]\d|3[1-6]|5[234]|6[2-46]))|3(?:3(?:2[79]|6\d|8[2578])|4(?:[78]\d|0[0124-9]|[1-35]\d|4[24-7]|6[02-9]|9[123678])|5(?:[138]\d|2[1245]|4[1-9]|6[2-4]|7[1-6])|6[24]\d|7(?:[0469]\d|1[1568]|2[013-9]|3[145]|5[14-8]|7[2-57]|8[0-24-9])|8(?:[013578]\d|2[15-7]|4[13-6]|6[1-357-9]|9[124]))|670\d)\d{6}$/',
            'mobile' => '/^675\d{7}|9(?:11[2-9]\d{7}|(?:2(?:2[013]|3[067]|49|6[01346]|80|9[147-9])|3(?:36|4[12358]|5[138]|6[24]|7[069]|8[013578]))[2-9]\d{6}|\d{4}[2-9]\d{5})$/',
            'tollfree' => '/^800\d{7}$/',
            'premium' => '/^60[04579]\d{7}$/',
            'uan' => '/^810\d{7}$/',
            'shortcode' => '/^1(?:0[2356]|1[02-5]|21)$/',
            'emergency' => '/^1(?:0[017]|28)$/',
        ),
        'possible' => array(
            'general' => '/^\d{6,11}$/',
            'fixed' => '/^\d{6,10}$/',
            'mobile' => '/^\d{6,11}$/',
            'tollfree' => '/^\d{10}$/',
            'premium' => '/^\d{10}$/',
            'uan' => '/^\d{10}$/',
            'shortcode' => '/^\d{3}$/',
            'emergency' => '/^\d{3}$/',
        ),
    ),
);
