<?php
return [
    'router' => [
        'routes' => [
            'barroute' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/bar-test',
                    'defaults' => [
                        'controller' => 'bar_index',
                        'action'     => 'unittests',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'bar_index' => 'Bar\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
