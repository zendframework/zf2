<?php return [
  'My\\DbAdapter' =>
  [
    'superTypes' =>
    [
    ],
    'instantiator' => '__construct',
    'methods' =>
    [
      '__construct' =>
      [
        'username' => NULL,
        'password' => NULL,
      ],
    ],
  ],
  'My\\EntityA' =>
  [
    'supertypes' =>
    [
    ],
    'instantiator' => NULL,
    'methods' =>
    [
    ],
  ],
  'My\\Mapper' =>
  [
    'supertypes' =>
    [
      0 => 'ArrayObject',
    ],
    'instantiator' => '__construct',
    'methods' =>
    [
      'setDbAdapter' =>
      [
        'dbAdapter' => 'My\\DbAdapter',
      ],
    ],
  ],
  'My\\RepositoryA' =>
  [
    'superTypes' =>
    [
    ],
    'instantiator' => '__construct',
    'injectionMethods' =>
    [
      'setMapper' =>
      [
        'mapper' => 'My\\Mapper',
      ],
    ],
  ],
  'My\\RepositoryB' =>
  [
    'superTypes' =>
    [
      0 => 'My\\RepositoryA',
    ],
    'instantiator' => NULL,
    'Methods' =>
    [
    ],
  ],
];
