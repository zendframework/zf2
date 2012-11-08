<?php

require_once __DIR__ . '/../../../../library/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'autoregister_zf' => true,
        'namespaces' => array(
            'ZendTest' => __DIR__ . '/../../ZendTest',
        ),
    ),
));