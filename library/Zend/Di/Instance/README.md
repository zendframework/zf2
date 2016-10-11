Dumper works as following:

```php
$di = new \Zend\Di\Di();
// ... configure $di ...

$dumper = new \Zend\Di\Instance\Dumper($di);

// produces a list of all defined class instance configurations and aliases
$dumper->getInitialInstanceDefinitions();

// produces a list of injections required for 'MyClassOrAlias' to be fetched
$dumper->getInjectedDefinitions('MyClassOrAlias');

// produces a list of injections required for 'item1', 'item2' to be fetched
$dumper->getInjectedDefinitions(array('item1', 'item2'));

// produces a list of injections required for all found instance configurations and aliases
$dumper->getAllInjectedDefinitions();
```

Dumped instance definitions look like following (todo)
```php
array(
    'class_or_alias' => array(
        'instantiator' => array(
            'name' => '__construct', // either '__construct' or a callback string/array (closures unsupported!)
            'parameters' => array(
                $injectionParameter1, // an instance of Zend\Di\Instance\Dumper\*
                $injectionParameter2, // an instance of Zend\Di\Instance\Dumper\*
                // other parameters
            ),
        ),
        'injections' => array(
            array(
                'name' => 'calledMethodName',
                'parameters' => array(
                    $injectionParameter3, // an instance of Zend\Di\Instance\Dumper\*
                    $injectionParameter4, // an instance of Zend\Di\Instance\Dumper\*
                    // ...
                ),
            ),
            array(
                'name' => 'anotherCalledMethodName',
                'parameters' => array(),
            ),
        ),
    ),
    // 'next_class_or_alias' => array( ... ),
);
```
