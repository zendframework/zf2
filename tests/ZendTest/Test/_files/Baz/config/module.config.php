<?php
return [
    'console' => [
        'router' => [
            'routes' => [
                'consoleroute' => [
                    'type' => 'simple',
                    'options' => [
                        'route'    => '--console',
                        'defaults' => [
                            'controller' => 'baz_index',
                            'action'     => 'console',
                        ],
                    ],
                ],
                'arguments' => [
                    'type' => 'simple',
                    'options' => [
                        'route'    => 'filter --date= --id= --text=',
                        'defaults' => [
                            'controller' => 'baz_index',
                            'action'     => 'console',
                        ],
                    ],
                ],
                'arguments-mandatory' => [
                    'type' => 'simple',
                    'options' => [
                        'route'    => 'foo --bar= --baz=',
                        'defaults' => [
                            'controller' => 'baz_index',
                            'action'     => 'console',
                        ],
                    ],
                ]
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'myroute' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/tests',
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'unittests',
                    ],
                ],
            ],
            'myroutebis' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/tests-bis',
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'unittests',
                    ],
                ],
            ],
            'persistence' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/tests-persistence',
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'persistencetest',
                    ],
                ],
            ],
            'exception' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/exception',
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'exception',
                    ],
                ],
            ],
            'redirect' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/redirect',
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'redirect',
                    ],
                ],
            ],
            'dnsroute' => [
                'type' => 'hostname',
                'options' => [
                    'route' => ':subdomain.domain.tld',
                    'constraints' => [
                        'subdomain' => '\w+'
                    ],
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'unittests',
                    ],
                ],
            ],
            'custom-response' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/custom-response',
                    'defaults' => [
                        'controller' => 'baz_index',
                        'action'     => 'custom-response',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'baz_index' => 'Baz\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'template_map' => [
            '404' => __DIR__ . '/../view/baz/error/404.phtml',
            'error' => __DIR__ . '/../view/baz/error/error.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
