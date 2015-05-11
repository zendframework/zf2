<?php
return [
    'modules' => [
        'Baz',
        'Foo',
        'Bar',
    ],
    'module_listener_options' => [
        'config_static_paths' => [],
        'module_paths'        => [
            'Baz' => __DIR__ . '/Baz/',
            'Foo' => __DIR__ . '/modules-path/with-subdir/Foo',
            'Bar' => __DIR__ . '/modules-path/with-subdir/Bar',
        ],
    ],
];
