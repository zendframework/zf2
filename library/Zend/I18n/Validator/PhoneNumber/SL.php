<?php
return array(
    'code' => '232',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-578]\\d{7}$/',
            'fixed' => '/^[235]2[2-4][2-9]\\d{4}$/',
            'mobile' => '/^(?:2[15]|3[034]|4[04]|5[05]|7[6-9]|88)\\d{6}$/',
            'emergency' => '/^(?:01|99)9$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6,8}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
