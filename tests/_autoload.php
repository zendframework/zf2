<?php
if (!include_once __DIR__ . '/../vendor/autoload.php') {
    throw new UnexpectedValueException('Coul not find composer, did you run `php composer.phar install`?');
}

require_once __DIR__ . '/../library/Zend/Loader/StandardAutoloader.php';

$loader = new Zend\Loader\StandardAutoloader(array(
    Zend\Loader\StandardAutoloader::LOAD_NS => array(
        'Zend'      => __DIR__ . '/../library',
        'ZendTest'  => __DIR__ . '/Zend',
    ),
));

$loader->register();