<?php
return [
    'modules' => [
        'Baz',
        //'Foo', // bar need Foo
        'Bar',
    ],
    'module_listener_options' => [
        'config_cache_enabled' => true,
        'cache_dir'            => __DIR__ . '/cache',
        'config_cache_key'     => 'phpunit',
        'config_static_paths'  => [],
        'module_paths'         => [
            'Baz' => __DIR__ . '/Baz/',
            'Foo' => __DIR__ . '/modules-path/with-subdir/Foo',
            'Bar' => __DIR__ . '/modules-path/with-subdir/Bar',
        ],
    ],
];
