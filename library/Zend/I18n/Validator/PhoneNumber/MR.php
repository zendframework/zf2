<?php
return array(
    'code' => '222',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-48]\\d{7}$/',
            'fixed' => '/^25[08]\\d{5}|35\\d{6}|45[1-7]\\d{5}$/',
            'mobile' => '/^(?:2(?:2\\d|70)|3(?:3\\d|6[1-36]|7[1-3])|4(?:4\\d|6[0457-9]|7[4-9]))\\d{5}$/',
            'tollfree' => '/^800\\d{5}$/',
            'emergency' => '/^1[78]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8}$/',
            'emergency' => '/^\\d{2}$/',
        ),
    ),
);
