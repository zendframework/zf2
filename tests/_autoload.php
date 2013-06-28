<?php
/**
 * Setup autoloading
 */

// if composer autoloader is missing, explicitly add the ZF library path
require_once __DIR__ . '/../library/Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(
    array(
        Zend\Loader\StandardAutoloader::LOAD_NS => array(
            'Zend'     => __DIR__ . '/../library/Zend',
            'ZendTest' => __DIR__ . '/ZendTest',
        ),
    ));
$loader->register();
}
