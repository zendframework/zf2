<?php
$ds       = DIRECTORY_SEPARATOR;
$basePath = realpath(__DIR__ . "$ds..");
return [
    'ZendTest\Loader\StandardAutoloaderTest' => $basePath . $ds . 'StandardAutoloaderTest.php',
    'ZendTest\Loader\ClassMapAutoloaderTest' => $basePath . $ds . 'ClassMapAutoloaderTest.php',
];
