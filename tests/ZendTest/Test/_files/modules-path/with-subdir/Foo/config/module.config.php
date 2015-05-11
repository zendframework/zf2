<?php
return [
    'router' => [
        'routes' => [
            'fooroute' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/foo-test',
                    'defaults' => [
                        'controller' => 'foo_index',
                        'action'     => 'unittests',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'foo_index' => 'Foo\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
