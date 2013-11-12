<?php

require_once __DIR__ . '/../vendor/autoload.php';

$nb_occur=30000;

$time_start = microtime(true);

$eventManager = new \Zend\EventManager\EventManager();


$eventManager->attach('event', function() {});
$eventManager->attach('event', function() {}, 2);

$eventManager->trigger('event');

for ($i=0 ; $i<$nb_occur; $i++)
{

}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo 'Durée : '.$time.' secondes<br/>';
